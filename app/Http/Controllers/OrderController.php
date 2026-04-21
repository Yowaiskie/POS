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
        
        $order = Order::firstOrCreate(
            ['order_type' => 'short', 'status' => 'open'],
            ['order_number' => 'SO-' . strtoupper(uniqid())]
        );

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

        return back()->with('success', "Added {$item->name} to cart.");
    }

    public function updateQuantity(Request $request, OrderItem $item)
    {
        $quantity = $item->quantity + ($request->delta ?? 0);
        
        if ($quantity <= 0) {
            $item->delete();
        } else {
            $item->update(['quantity' => $quantity]);
        }

        return back();
    }

    public function removeItem(OrderItem $item)
    {
        $item->delete();
        return back();
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
