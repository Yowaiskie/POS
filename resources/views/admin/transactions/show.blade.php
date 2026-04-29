@extends('layouts.app')

@section('content')
<div class="p-4 md:p-8 max-w-5xl mx-auto space-y-6">

    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.transactions.index') }}" class="p-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-500 transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    Transaction Details
                    @if(in_array($order->status, ['completed', 'paid', 'active']))
                        <span class="inline-flex bg-green-100 text-green-800 px-2.5 py-0.5 rounded-full text-xs font-medium">{{ ucfirst($order->status) }}</span>
                    @elseif($order->status === 'voided')
                        <span class="inline-flex bg-red-100 text-red-800 px-2.5 py-0.5 rounded-full text-xs font-medium">Voided</span>
                    @else
                        <span class="inline-flex bg-gray-100 text-gray-800 px-2.5 py-0.5 rounded-full text-xs font-medium">{{ ucfirst($order->status) }}</span>
                    @endif
                </h1>
                <p class="text-sm text-gray-500 font-mono">{{ $order->transaction_id }}</p>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.transactions.receipt', $order) }}" target="_blank" class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-medium text-sm hover:bg-indigo-700 transition-colors shadow-sm flex items-center gap-2">
                <i data-lucide="printer" class="w-4 h-4"></i> Reprint Receipt
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- Left Column: Transaction Breakdown -->
    <div class="md:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-900 flex items-center gap-2">
                    <i data-lucide="shopping-cart" class="w-4 h-4 text-gray-500"></i> Order Items
                </h3>
                <span class="text-xs font-medium text-gray-500">
                    {{ $order->items->sum('quantity') }} Items Total
                </span>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($order->items as $item)
                    <div class="px-6 py-4 flex justify-between items-center {{ $item->is_voided ? 'opacity-50' : '' }}">
                        <div class="flex items-start gap-4">
                            <div class="bg-gray-100 rounded text-gray-700 font-bold px-2 py-1 text-sm border border-gray-200">
                                {{ $item->quantity }}x
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 {{ $item->is_voided ? 'line-through' : '' }}">{{ $item->name }}</h4>
                                <div class="text-xs text-gray-500">₱{{ number_format($item->unit_price, 2) }} each</div>
                                @if($item->is_voided)
                                    <span class="text-[10px] font-bold text-red-600 uppercase mt-1 block">Voided</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold text-gray-900">₱{{ number_format($item->unit_price * $item->quantity, 2) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                @if($order->order_type === 'room' && $order->roomSession)
                    <div class="flex justify-between items-center py-2 text-sm">
                        <span class="text-gray-600">Room Base Charge ({{ $order->roomSession->room->name }})</span>
                        <span class="font-medium text-gray-900">Included in total</span>
                    </div>
                @endif
                <div class="flex justify-between items-center py-3 border-t border-gray-200 mt-2">
                    <span class="font-bold text-lg text-gray-900">Grand Total</span>
                    <span class="font-bold text-2xl text-indigo-700">₱{{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Meta Info -->
    <div class="space-y-6">
        
        <!-- Payment Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                    <i data-lucide="wallet" class="w-4 h-4 text-gray-500"></i> Payment Details
                </h3>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <div class="text-xs text-gray-500 mb-1">Method</div>
                    <div class="font-medium text-gray-900 uppercase inline-flex items-center gap-1">
                        @if($order->payment_method === 'cash')
                            <i data-lucide="banknote" class="w-4 h-4 text-green-600"></i> Cash
                        @elseif($order->payment_method === 'gcash')
                            <i data-lucide="smartphone" class="w-4 h-4 text-blue-600"></i> GCash
                        @else
                            {{ $order->payment_method ?? 'N/A' }}
                        @endif
                    </div>
                </div>
                @if($order->reference_number)
                <div>
                    <div class="text-xs text-gray-500 mb-1">Reference No.</div>
                    <div class="font-mono text-sm text-gray-900">{{ $order->reference_number }}</div>
                </div>
                @endif
                <div>
                    <div class="text-xs text-gray-500 mb-1">Amount Received</div>
                    <div class="font-medium text-gray-900">₱{{ number_format($order->amount_received, 2) }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Change</div>
                    <div class="font-medium text-gray-900">₱{{ number_format(max(0, $order->amount_received - $order->total_amount), 2) }}</div>
                </div>
            </div>
        </div>

        <!-- Shift & Staff Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                    <i data-lucide="user" class="w-4 h-4 text-gray-500"></i> System Info
                </h3>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <div class="text-xs text-gray-500 mb-1">Processed By (Cashier)</div>
                    <div class="font-medium text-gray-900">{{ $order->user->name ?? 'System' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Shift ID</div>
                    <div class="font-medium text-indigo-600">
                        @if($order->shift_id)
                            #{{ $order->shift_id }}
                        @else
                            <span class="text-gray-400">None</span>
                        @endif
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Transaction Date</div>
                    <div class="font-medium text-gray-900">{{ $order->created_at->format('M d, Y h:i:s A') }}</div>
                </div>
                @if($order->closed_at)
                <div>
                    <div class="text-xs text-gray-500 mb-1">Closed At</div>
                    <div class="font-medium text-gray-900">{{ $order->closed_at->format('M d, Y h:i:s A') }}</div>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
