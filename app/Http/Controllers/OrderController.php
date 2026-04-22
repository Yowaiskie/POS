<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $categories = MenuCategory::orderBy('id')->get();
        $selectedCategory = request('category', $categories->first()?->slug);

        $itemsQuery = MenuItem::with('category')->where('is_active', true);
        if ($selectedCategory) {
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
                ['order_number' => 'RO-' . strtoupper(uniqid()), 'user_id' => $userId]
            );
        } else {
            $order = Order::firstOrCreate(
                ['order_type' => 'short', 'status' => 'open'],
                ['order_number' => 'SO-' . strtoupper(uniqid()), 'user_id' => $userId]
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
            $orderItem->increment('quantity');
        } else {
            $order->items()->create([
                'menu_item_id' => $item->id,
                'name'         => $item->name,
                'quantity'     => 1,
                'unit_price'   => $item->price,
            ]);
        }

        $redirect = back()->with('success', "Added {$item->name} to cart.");

        if ($order->room_session_id) {
            $redirect->with('open_modal_for_session', $order->room_session_id);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Added {$item->name} to cart.",
                'cart' => $this->getCartData()
            ]);
        }

        return $redirect;
    }

    public function updateQuantity(Request $request, OrderItem $item)
    {
        $delta    = $request->delta ?? 0;
        $quantity = $item->quantity + $delta;
        $order    = $item->order;

        // When increasing, check available stock
        if ($delta > 0 && $item->menuItem) {
            $menuItem = $item->menuItem;
            if ($menuItem->stock_quantity !== null && $quantity > $menuItem->stock_quantity) {
                $errorMsg = "Not enough stock for \"{$menuItem->name}\". Only {$menuItem->stock_quantity} available.";
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $errorMsg], 422);
                }
                $redirect = back()->with('error', $errorMsg);
                if ($order && $order->room_session_id) {
                    $redirect->with('open_modal_for_session', $order->room_session_id);
                }
                return $redirect;
            }
        }

        if ($quantity <= 0) {
            $item->delete();
        } else {
            $item->update(['quantity' => $quantity]);
        }

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

        return $redirect;
    }

    public function removeItem(OrderItem $item)
    {
        $order = $item->order;
        $item->delete();

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
        $order = Order::with('items.menuItem')
            ->where('order_type', 'short')
            ->where('status', 'open')
            ->first();

        if (!$order) return back();

        // --- Stock validation & deduction at checkout ---
        foreach ($order->items as $orderItem) {
            $menuItem = $orderItem->menuItem;
            if (!$menuItem || $menuItem->stock_quantity === null) {
                continue; // unlimited — skip
            }
            if ($menuItem->stock_quantity < $orderItem->quantity) {
                return back()->with('error', "Cannot complete order: \"{$menuItem->name}\" only has {$menuItem->stock_quantity} left in stock.");
            }
        }

        // All stock checks passed — deduct now
        foreach ($order->items as $orderItem) {
            $menuItem = $orderItem->menuItem;
            if ($menuItem && $menuItem->stock_quantity !== null) {
                $menuItem->decrement('stock_quantity', $orderItem->quantity);
            }
        }

        $order->update([
            'status'          => 'paid',
            'payment_method'  => $request->payment_method ?? 'cash',
            'amount_received' => $request->amount_received ?? $order->total_amount,
            'closed_at'       => now(),
            'user_id'         => $order->user_id ?? (auth()->id() ?? 1),
        ]);

        return back()->with('success', 'Order completed successfully.');
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
