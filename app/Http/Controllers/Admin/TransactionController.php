<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Activity;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items', 'shift'])
            ->where('status', '!=', 'open'); // Exclude active orders

        // Search by Transaction ID
        if ($request->filled('search')) {
            $query->where('transaction_id', 'like', '%' . $request->search . '%');
        }

        // Filter by Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by Payment Method
        if ($request->filled('payment_method') && $request->payment_method !== 'all') {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by Order Type
        if ($request->filled('order_type') && $request->order_type !== 'all') {
            $query->where('order_type', $request->order_type);
        }

        // Filter by Cashier (User)
        if ($request->filled('user_id') && $request->user_id !== 'all') {
            $query->where('user_id', $request->user_id);
        }

        // Filter by Shift ID
        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        
        $users = \App\Models\User::all();

        return view('admin.transactions.index', compact('transactions', 'users'));
    }

    public function voids(Request $request)
    {
        $query = OrderItem::with(['order', 'menuItem'])
            ->where('is_voided', true);

        // Search by related Transaction ID
        if ($request->filled('search')) {
            $query->whereHas('order', function($q) use ($request) {
                $q->where('transaction_id', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by Voiding Admin/User
        if ($request->filled('voided_by') && $request->voided_by !== 'all') {
            $query->where('voided_by', $request->voided_by);
        }

        $voids = $query->orderBy('voided_at', 'desc')->paginate(20)->withQueryString();
        $users = \App\Models\User::where('position', 'Admin')->get();

        return view('admin.transactions.voids', compact('voids', 'users'));
    }

    public function show(Order $order)
    {
        $order->load(['items.menuItem', 'user', 'shift', 'roomSession.room']);

        // Log audit trail
        Activity::create([
            'action' => "Admin viewed transaction {$order->transaction_id}",
            'occurred_at' => now(),
        ]);

        return view('admin.transactions.show', compact('order'));
    }

    public function receipt(Order $order)
    {
        $order->load(['items', 'user', 'roomSession.room']);
        
        // Ensure this is a reprint
        $isReprint = true;

        return view('orders.receipt', compact('order', 'isReprint'));
    }
}
