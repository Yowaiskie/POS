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

        return view('orders.index', [
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'items' => $items,
            'activeOrder' => $activeOrder,
            'activeOrderItems' => $activeOrderItems,
            'activeOrderTotal' => $activeOrder?->total_amount ?? 0,
            'activeOrderItemCount' => $activeOrder?->items->sum('quantity') ?? 0,
        ]);
    }

    public function addItem(Request $request)
    {
        $item = MenuItem::findOrFail($request->menu_item_id);
        
        if ($request->has('room_session_id')) {
            $order = Order::firstOrCreate(
                ['room_session_id' => $request->room_session_id, 'status' => 'open', 'order_type' => 'room'],
                ['order_number' => 'RO-' . strtoupper(uniqid())]
            );
        } else {
            $order = Order::firstOrCreate(
                ['order_type' => 'short', 'status' => 'open'],
                ['order_number' => 'SO-' . strtoupper(uniqid())]
            );
        }

        $orderItem = $order->items()->where('menu_item_id', $item->id)->first();

        if ($orderItem) {
            $orderItem->increment('quantity');
        } else {
            $order->items()->create([
                'menu_item_id' => $item->id,
                'name' => $item->name,
                'quantity' => 1,
                'unit_price' => $item->price,
            ]);
        }

        $redirect = back()->with('success', "Added {$item->name} to cart.");
        
        if ($order->room_session_id) {
            $redirect->with('open_modal_for_session', $order->room_session_id);
        }

        return $redirect;
    }

    public function updateQuantity(Request $request, OrderItem $item)
    {
        $quantity = $item->quantity + ($request->delta ?? 0);
        $order = $item->order;
        
        if ($quantity <= 0) {
            $item->delete();
        } else {
            $item->update(['quantity' => $quantity]);
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
        
        $redirect = back();
        if ($order && $order->room_session_id) {
            $redirect->with('open_modal_for_session', $order->room_session_id);
        }

        return $redirect;
    }

    public function checkout(Request $request)
    {
        $order = Order::where('order_type', 'short')->where('status', 'open')->first();
        if (!$order) return back();

        $order->update([
            'status' => 'paid',
            'payment_method' => $request->payment_method ?? 'cash',
            'amount_received' => $request->amount_received ?? $order->total_amount,
            'closed_at' => now(),
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
        return back();
    }
}
