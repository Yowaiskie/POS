<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Room;
use App\Models\RoomSession;
use App\Models\PromoSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with(['activeSession.orders.items.menuItem'])
            ->orderBy('name')
            ->get();

        $categories = MenuCategory::withCount('items')->orderBy('id')->get();
        $items = MenuItem::with('category')->where('is_active', true)->orderBy('name')->get();
        $promoSets = PromoSet::with('items.menuItem')->where('is_active', true)->get();

        return view('rooms.index', [
            'rooms' => $rooms,
            'categories' => $categories,
            'items' => $items,
            'promoSets' => $promoSets,
        ]);
    }

    public function startSession(Request $request, Room $room)
    {
        if ($room->activeSession) {
            return back()->with('error', 'Room is already occupied.');
        }

        try {
            DB::transaction(function () use ($request, $room) {
                $duration = $request->input('duration', 1);
                $promoSetId = $request->input('promo_set_id');
                
                $promoSet = null;
                if ($promoSetId) {
                    $promoSet = PromoSet::with('items')->find($promoSetId);
                    if ($promoSet) {
                        $duration = $promoSet->duration_hours;
                    }
                }

                $session = $room->sessions()->create([
                    'started_at' => now(),
                    'ends_at' => now()->addHours($duration),
                    'status' => 'active',
                ]);

                if ($promoSet) {
                    $transactionId = 'TRX-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
                    
                    $order = $session->orders()->create([
                        'order_type' => 'room',
                        'user_id' => auth()->id() ?? 1,
                        'status' => 'open',
                        'transaction_id' => $transactionId,
                        'promo_name' => $promoSet->name,
                        'promo_price' => $promoSet->price,
                    ]);

                    // Add the Promo Set itself as a charge
                    $order->items()->create([
                        'name' => $promoSet->name . ' (Promo Charge)',
                        'unit_price' => $promoSet->price,
                        'quantity' => 1,
                    ]);

                    // Add included items
                    foreach ($promoSet->items as $promoItem) {
                        if ($promoItem->menuItem) {
                            $order->items()->create([
                                'menu_item_id' => $promoItem->menu_item_id,
                                'name' => $promoItem->menuItem->name . ' (Promo Included)',
                                'unit_price' => 0,
                                'quantity' => $promoItem->quantity,
                                'is_included_in_promo' => true,
                            ]);
                        }
                    }
                }
            });

            return back()->with('success', "Session started for {$room->name}");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to start session: ' . $e->getMessage());
        }
    }

    public function extendSession(RoomSession $session)
    {
        $duration = request('duration', 30);
        $newEndsAt = $session->ends_at ? $session->ends_at->addMinutes($duration) : now()->addMinutes($duration);

        $session->update([
            'ends_at' => $newEndsAt,
            'status' => 'active', // Reset to active if it was warning/overtime
        ]);

        return back()->with('success', "Extended session by {$duration} minutes.");
    }

    public function billOut(Request $request, RoomSession $session)
    {
        if ($request->payment_method === 'gcash') {
            $request->validate([
                'reference_number' => 'required|digits:13',
            ], [
                'reference_number.digits' => 'GCash Reference Number must be exactly 13 digits.',
            ]);
        }

        $openOrders = $session->orders()->where('status', 'open')->with('items.menuItem')->get();

        // Stock validation
        foreach ($openOrders as $order) {
            foreach ($order->items as $orderItem) {
                $menuItem = $orderItem->menuItem;
                if ($menuItem && $menuItem->stock_quantity !== null) {
                    if ($menuItem->stock_quantity < $orderItem->quantity) {
                        return back()->with('error', "Cannot complete bill out: \"{$menuItem->name}\" only has {$menuItem->stock_quantity} left in stock.");
                    }
                }
            }
        }

        // Deduct stock
        foreach ($openOrders as $order) {
            foreach ($order->items as $orderItem) {
                $menuItem = $orderItem->menuItem;
                if ($menuItem && $menuItem->stock_quantity !== null) {
                    $menuItem->decrement('stock_quantity', $orderItem->quantity);
                }
            }
        }

        $session->update([
            'status' => 'completed',
            'ends_at' => now(),
        ]);

        $userId = auth()->id() ?? 1;

        foreach ($openOrders as $order) {
            $order->update([
                'status'           => 'paid',
                'payment_method'   => $request->payment_method ?? 'cash',
                'amount_received'  => $request->amount_received,
                'reference_number' => $request->reference_number,
                'closed_at'        => now(),
                'user_id'          => $order->user_id ?? $userId,
            ]);
        }

        return back()->with('success', "Room billed out successfully.");
    }
}
