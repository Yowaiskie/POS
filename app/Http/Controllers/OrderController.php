<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\OrderFlowService;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderFlowService;
    protected $inventoryService;

    public function __construct(OrderFlowService $orderFlowService, InventoryService $inventoryService)
    {
        $this->orderFlowService = $orderFlowService;
        $this->inventoryService = $inventoryService;
    }

    public function index()
    {
        $categories = MenuCategory::orderBy('id')->get();
        $selectedCategory = request('category', 'all');

        $itemsQuery = MenuItem::with('category')->where('is_active', true);
        if ($selectedCategory !== 'all') {
            $itemsQuery->whereHas('category', function ($query) use ($selectedCategory) {
                $query->where('slug', $selectedCategory);
            });
        }

        $items = $itemsQuery->orderBy('name')->get();

        $activeOrder = Order::with('items')
            ->where('order_type', 'short')
            ->where('status', 'open')
            ->latest()
            ->first();

        $activeOrderItems = $activeOrder?->items ?? collect();

        // Build a map of menu_item_id => quantity already in cart
        $cartQuantities = $activeOrderItems->pluck('quantity', 'menu_item_id');

        return view('orders.index', [
            'categories'           => $categories,
            'selectedCategory'     => $selectedCategory,
            'items'                => $items,
            'activeOrder'          => $activeOrder,
            'activeOrderItems'     => $activeOrderItems,
            'activeOrderTotal'     => $activeOrder?->total_amount ?? 0,
            'activeOrderItemCount' => $activeOrder?->items->sum('quantity') ?? 0,
            'cartQuantities'       => $cartQuantities,
        ]);
    }

    public function addItem(Request $request)
    {
        $item = MenuItem::findOrFail($request->menu_item_id);

        // --- Stock availability check (no deduction yet) ---
        if ($item->isOutOfStock()) {
            return back()->with('error', "\"{$item->name}\" is out of stock.");
        }

        $userId = auth()->id() ?? 1;

        if ($request->has('room_session_id')) {
            $order = Order::firstOrCreate(
                ['room_session_id' => $request->room_session_id, 'status' => 'open', 'order_type' => 'room'],
                [
                    'order_number' => 'RO-' . strtoupper(uniqid()), 
                    'user_id' => $userId,
                    'transaction_id' => 'TRX-' . now()->format('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6))
                ]
            );
        } else {
            $order = Order::firstOrCreate(
                ['order_type' => 'short', 'status' => 'open'],
                [
                    'order_number' => 'SO-' . strtoupper(uniqid()), 
                    'user_id' => $userId,
                    'transaction_id' => 'TRX-' . now()->format('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6))
                ]
            );
        }

        $orderItem = $order->items()->where('menu_item_id', $item->id)->first();

        // Check if adding one more would exceed available stock
        if ($item->stock_quantity !== null) {
            $currentQtyInCart = $orderItem ? $orderItem->quantity : 0;
            if ($currentQtyInCart + 1 > $item->stock_quantity) {
                return back()->with('error', "Not enough stock for \"{$item->name}\". Only {$item->stock_quantity} available.");
            }
        }

        if ($orderItem) {
            $this->inventoryService->adjustStock($orderItem, $orderItem->quantity + 1);
            $orderItem->increment('quantity');
        } else {
            $orderItem = $order->items()->create([
                'menu_item_id' => $item->id,
                'name'         => $item->name,
                'quantity'     => 1,
                'unit_price'   => $item->price,
            ]);

            // [NEW] Immediate deduction for Room Orders
            if ($order->order_type === 'room') {
                $this->inventoryService->deductStock($orderItem);
            }
        }

        $redirect = back()->with('success', "Added {$item->name} to cart.");

        if ($order->room_session_id) {
            $redirect->with('open_modal_for_session', $order->room_session_id);
        }

        if ($request->wantsJson()) {
            $data = [
                'success' => true,
                'message' => "Added {$item->name} to cart.",
                'cart'    => $this->getCartData()
            ];

            if ($order->room_session_id) {
                $session = \App\Models\RoomSession::with(['room', 'orders.items.menuItem'])->find($order->room_session_id);
                
                // Calculate dynamic totals using Billing Service
                $billingService = app(\App\Services\RoomBillingService::class);
                $pricingSnapshot = $session->pricing_snapshot ? json_decode($session->pricing_snapshot) : null;
                $chargeResult = $billingService->calculateCharge($session, $pricingSnapshot);
                
                $foodTotal = $session->orders->where('status', 'active')->sum(fn($o) => $o->items->sum(fn($i) => $i->unit_price * $i->quantity));
                $session->food_total = $foodTotal;
                $session->room_charge = $chargeResult['charge'];
                $session->total_amount = $session->room_charge + $foodTotal;
                
                $data['session'] = $session;
            }

            return response()->json($data);
        }

        return $redirect;
    }

    public function updateQuantity(Request $request, OrderItem $item)
    {
        if ($request->has('quantity')) {
            $quantity = (int)$request->quantity;
        } else {
            $delta    = $request->delta ?? 0;
            $quantity = $item->quantity + $delta;
        }

        $currentQty = $item->quantity;

        // If zero or less, treat as removal
        if ($quantity <= 0) {
            return $this->removeItem($request, $item);
        }

        $order = $item->order;

        try {
            $updateUser = auth()->user();
            if ($request->has('admin_id')) {
                $updateUser = \App\Models\User::find($request->admin_id) ?? $updateUser;
            }

            // If decreasing and already deducted, only admin can authorize
            if ($quantity < currentQty && $item->is_stock_deducted) {
                if ($updateUser->position !== 'Admin') {
                    return back()->with('error', 'Only Admin can authorize quantity reduction for committed items.');
                }
            }

            // Adjust stock via service
            $this->inventoryService->adjustStock($item, $quantity);

            $item->update(['quantity' => $quantity]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'cart' => $this->getCartData()
                ]);
            }

            $redirect = back();
            if ($order && $order->room_session_id) {
                $redirect->with('open_modal_for_session', $order->room_session_id);
            }
            return $redirect->with('success', 'Quantity updated successfully.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function removeItem(Request $request, OrderItem $item)
    {
        $order = $item->order;
        
        try {
            // If it's already deducted, we treat 'remove' as 'void'
            if ($item->is_stock_deducted) {
                $voidUser = auth()->user();
                if (request()->has('admin_id')) {
                    $voidUser = \App\Models\User::find(request()->admin_id) ?? $voidUser;
                }
                $this->inventoryService->voidItem($item, $voidUser);
            } else {
                $item->delete();
            }
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'cart' => $this->getCartData()
            ]);
        }

        $redirect = back();
        if ($order && $order->room_session_id) {
            $redirect->with('open_modal_for_session', $order->room_session_id);
        }

        return $redirect;
    }

    public function voidItem(OrderItem $item)
    {
        try {
            $voidUser = auth()->user();
            if (request()->has('admin_id')) {
                $voidUser = \App\Models\User::find(request()->admin_id) ?? $voidUser;
            }
            $this->inventoryService->voidItem($item, $voidUser);
            return back()->with('success', 'Item voided successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Helper to get common cart data for AJAX responses.
     */
    private function getCartData()
    {
        $order = Order::with('items.menuItem')
            ->where('order_type', 'short')
            ->where('status', 'open')
            ->latest()
            ->first();

        if (!$order) {
            return [
                'items' => [],
                'total' => 0,
                'count' => 0
            ];
        }

        return [
            'items' => $order->items->map(fn($i) => [
                'id' => $i->id,
                'name' => $i->name,
                'quantity' => $i->quantity,
                'unit_price' => (float)$i->unit_price,
                'total' => (float)($i->unit_price * $i->quantity),
                'menu_item_id' => $i->menu_item_id,
                'stock_quantity' => $i->menuItem?->stock_quantity
            ]),
            'total' => (float)$order->total_amount,
            'count' => (int)$order->items->sum('quantity')
        ];
    }

    public function checkout(Request $request)
    {
        if ($request->payment_method === 'gcash') {
            $request->validate([
                'reference_number' => 'required|digits:13',
            ], [
                'reference_number.digits' => 'GCash Reference Number must be exactly 13 digits.',
            ]);
        }

        $order = Order::with('items.menuItem')
            ->where('order_type', 'short')
            ->where('status', 'open')
            ->first();

        if (!$order) {
            return back()->with('error', 'No open order found or already completed.');
        }

        try {
            $this->orderFlowService->checkoutShortOrder($order, $request->all());
            return back()->with('success', 'Order completed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function clear()
    {
        $order = Order::where('order_type', 'short')->where('status', 'open')->first();
        if ($order) {
            $order->items()->delete();
            $order->delete();
        }

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'cart' => $this->getCartData()
            ]);
        }

        return back();
    }
}
