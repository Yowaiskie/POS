<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Room;
use App\Models\RoomSession;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with(['activeSession.orders.items.menuItem'])
            ->orderBy('name')
            ->get();

        $categories = MenuCategory::withCount('items')->orderBy('id')->get();
        $items = MenuItem::with('category')->where('is_active', true)->orderBy('name')->get();

        return view('rooms.index', [
            'rooms' => $rooms,
            'categories' => $categories,
            'items' => $items,
        ]);
    }

    public function startSession(Room $room)
    {
        if ($room->activeSession) {
            return back()->with('error', 'Room is already occupied.');
        }

        $room->sessions()->create([
            'started_at' => now(),
            'ends_at' => now()->addHour(), // Default 1 hour
            'status' => 'active',
        ]);

        return back()->with('success', "Session started for {$room->name}");
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
                'status' => 'paid',
                'payment_method' => $request->payment_method ?? 'cash',
                'closed_at' => now(),
                'user_id' => $order->user_id ?? $userId,
            ]);
        }

        return back()->with('success', "Room billed out successfully.");
    }
}
