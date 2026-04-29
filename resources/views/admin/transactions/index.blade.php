@extends('layouts.app')

@section('content')
<div class="p-4 md:p-8 max-w-7xl mx-auto space-y-6" x-data="{ quickViewModalOpen: false, quickViewOrder: null }">

    <div class="mb-2">
        <h1 class="text-2xl font-bold text-gray-900">Transaction History</h1>
        <p class="text-sm text-gray-500">Audit trail of all POS transactions</p>
    </div>

    <!-- Tabs -->
    <div class="flex space-x-4 border-b border-gray-200">
        <a href="{{ route('admin.transactions.index') }}" class="px-4 py-2 border-b-2 font-medium text-sm {{ request()->routeIs('admin.transactions.index') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            Completed Transactions
        </a>
        <a href="{{ route('admin.transactions.voids') }}" class="px-4 py-2 border-b-2 font-medium text-sm {{ request()->routeIs('admin.transactions.voids') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            Void History
        </a>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm font-bold text-gray-700">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                </div>
                Filter Transactions
            </div>
            @if(request()->anyFilled(['search', 'date_from', 'date_to', 'order_type', 'payment_method', 'user_id', 'shift_id']))
                <a href="{{ route('admin.transactions.index') }}" class="text-xs text-rose-600 hover:text-rose-800 font-bold flex items-center gap-1 transition-colors">
                    <i data-lucide="rotate-ccw" class="w-3 h-3"></i> Reset All Filters
                </a>
            @endif
        </div>
        
        <div class="p-6">
            <form action="{{ route('admin.transactions.index') }}" method="GET" class="space-y-6">
                <!-- Top Row: Search and Date -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- Search Field -->
                    <div class="lg:col-span-5">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Search Transaction</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i data-lucide="search" class="w-4 h-4 text-gray-400 group-focus-within:text-indigo-500 transition-colors"></i>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Enter Transaction ID (TRX-...)" 
                                   class="block w-full pl-11 h-12 bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all placeholder:text-gray-400">
                        </div>
                    </div>

                    <!-- Date Range Fields -->
                    <div class="lg:col-span-7">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Date Range</label>
                        <div class="flex flex-col sm:flex-row items-center gap-3">
                            <div class="relative flex-1 w-full">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
                                </div>
                                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                                       class="block w-full pl-11 h-12 bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                            </div>
                            <div class="text-gray-300 font-bold text-xs uppercase hidden sm:block">to</div>
                            <div class="relative flex-1 w-full">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
                                </div>
                                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                                       class="block w-full pl-11 h-12 bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Row: Multi-select Filters -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 pt-6 border-t border-gray-50">
                    <!-- Type Filter -->
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1">Order Type</label>
                        <select name="order_type" class="block w-full h-11 bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                            <option value="all">All Types</option>
                            <option value="short" {{ request('order_type') === 'short' ? 'selected' : '' }}>Short Order</option>
                            <option value="room" {{ request('order_type') === 'room' ? 'selected' : '' }}>Room Bill</option>
                        </select>
                    </div>

                    <!-- Payment Filter -->
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1">Payment Method</label>
                        <select name="payment_method" class="block w-full h-11 bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                            <option value="all">All Methods</option>
                            <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="gcash" {{ request('payment_method') === 'gcash' ? 'selected' : '' }}>GCash</option>
                        </select>
                    </div>

                    <!-- Cashier Filter -->
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1">Processed By</label>
                        <select name="user_id" class="block w-full h-11 bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                            <option value="all">All Cashiers</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Shift Filter -->
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1">Shift Reference</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-400 text-xs font-bold">#</span>
                            </div>
                            <input type="text" name="shift_id" value="{{ request('shift_id') }}" 
                                   placeholder="Shift ID" 
                                   class="block w-full pl-7 h-11 bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="lg:pt-5.5">
                        <button type="submit" class="w-full bg-indigo-600 text-white h-11 px-6 rounded-xl font-bold text-sm hover:bg-indigo-700 active:scale-[0.98] transition-all shadow-md shadow-indigo-200 flex items-center justify-center gap-2">
                            <i data-lucide="search" class="w-4 h-4"></i>
                            Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Time</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cashier</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div>{{ $trx->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $trx->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                {{ $trx->transaction_id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($trx->shift_id)
                                    #{{ $trx->shift_id }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($trx->order_type === 'room')
                                    <span class="inline-flex items-center gap-1 text-purple-700 bg-purple-100 px-2 py-0.5 rounded-full text-xs font-medium">
                                        <i data-lucide="door-open" class="w-3 h-3"></i> Room Bill
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-blue-700 bg-blue-100 px-2 py-0.5 rounded-full text-xs font-medium">
                                        <i data-lucide="coffee" class="w-3 h-3"></i> Short Order
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $trx->user->name ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-900">
                                ₱{{ number_format($trx->total_amount, 2) }}
                                <div class="text-[10px] text-gray-500 uppercase">{{ $trx->payment_method ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                @if(in_array($trx->status, ['completed', 'paid', 'active']))
                                    <span class="inline-flex bg-green-100 text-green-800 px-2 py-0.5 rounded-full text-xs font-medium">{{ ucfirst($trx->status) }}</span>
                                @elseif($trx->status === 'voided')
                                    <span class="inline-flex bg-red-100 text-red-800 px-2 py-0.5 rounded-full text-xs font-medium">Voided</span>
                                @else
                                    <span class="inline-flex bg-gray-100 text-gray-800 px-2 py-0.5 rounded-full text-xs font-medium">{{ ucfirst($trx->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                @php
                                    $quickViewData = [
                                        'id' => $trx->id,
                                        'transaction_id' => $trx->transaction_id,
                                        'date' => $trx->created_at->format('M d, Y h:i A'),
                                        'amount' => '₱' . number_format($trx->total_amount, 2),
                                        'items' => $trx->items->map(fn($i) => ['name' => $i->name, 'qty' => $i->quantity, 'price' => (float)$i->unit_price])->values()->toArray()
                                    ];
                                @endphp
                                <button type="button" 
                                        class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-2 py-1 rounded transition-colors"
                                        @click="quickViewOrder = {{ Js::from($quickViewData) }}; quickViewModalOpen = true;">
                                    Quick View
                                </button>
                                <a href="{{ route('admin.transactions.show', $trx) }}" class="text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 px-2 py-1 rounded transition-colors inline-block">
                                    Full Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i data-lucide="receipt" class="w-10 h-10 text-gray-300 mb-2"></i>
                                    <p>No transactions found matching your criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($transactions->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>

    <!-- Quick View Modal -->
    <div x-show="quickViewModalOpen" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div x-show="quickViewModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="quickViewModalOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     @click.away="quickViewModalOpen = false"
                     class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i data-lucide="receipt-text" class="h-5 w-5 text-indigo-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title" x-text="quickViewOrder?.transaction_id"></h3>
                                <div class="mt-1 text-sm text-gray-500 flex justify-between">
                                    <span x-text="quickViewOrder?.date"></span>
                                </div>
                                
                                <div class="mt-4 border-t border-gray-100 pt-4 max-h-60 overflow-y-auto">
                                    <template x-for="item in quickViewOrder?.items" :key="item.name">
                                        <div class="flex justify-between items-center py-2 text-sm">
                                            <div class="flex items-center gap-2">
                                                <span class="font-medium text-gray-900" x-text="item.qty + 'x'"></span>
                                                <span class="text-gray-700" x-text="item.name"></span>
                                            </div>
                                            <span class="text-gray-900">₱<span x-text="(item.qty * item.price).toFixed(2)"></span></span>
                                        </div>
                                    </template>
                                </div>
                                
                                <div class="mt-4 border-t border-gray-200 pt-4 flex justify-between items-center font-bold text-lg">
                                    <span>Total</span>
                                    <span class="text-indigo-700" x-text="quickViewOrder?.amount"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <a :href="'/admin/transactions/' + quickViewOrder?.id" class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto">
                            View Full Page
                        </a>
                        <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" @click="quickViewModalOpen = false">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
