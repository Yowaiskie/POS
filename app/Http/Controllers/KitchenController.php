<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    public function index()
    {
        // Get all active orders that have at least one pending item
        $orders = Order::with(['items.menuItem', 'roomSession.room'])
            ->whereIn('status', ['open', 'paid'])
            ->whereHas('items', function ($query) {
                $query->where('kitchen_status', 'pending');
            })
            ->latest()
            ->get();

        return view('kitchen.index', compact('orders'));
    }

    public function serve(Request $request, $id)
    {
        $item = OrderItem::findOrFail($id);
        
        if ($request->wantsJson() || $request->isJson()) {
            $served = $request->input('served', true);
            $item->update(['kitchen_status' => $served ? 'served' : 'pending']);
            return response()->json(['success' => true, 'status' => $item->kitchen_status]);
        }

        $item->update(['kitchen_status' => 'served']);
        return back()->with('success', 'Item marked as served.');
    }

    public function serveOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->items()->update(['kitchen_status' => 'served']);
        
        if ($request->wantsJson() || $request->isJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Order marked as completed.');
    }
}
