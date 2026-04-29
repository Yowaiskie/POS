@extends('layouts.app')

@section('content')
<div class="p-4 md:p-8 max-w-7xl mx-auto space-y-6">

    <div class="mb-2">
        <h1 class="text-2xl font-bold text-gray-900">Void History</h1>
        <p class="text-sm text-gray-500">Audit trail of all voided items and orders</p>
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
                <div class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                </div>
                Filter Void Logs
            </div>
            @if(request()->anyFilled(['search', 'date_from', 'date_to', 'voided_by']))
                <a href="{{ route('admin.transactions.voids') }}" class="text-xs text-rose-600 hover:text-rose-800 font-bold flex items-center gap-1 transition-colors">
                    <i data-lucide="rotate-ccw" class="w-3 h-3"></i> Reset All Filters
                </a>
            @endif
        </div>
        
        <div class="p-6">
            <form action="{{ route('admin.transactions.voids') }}" method="GET" class="space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- Search Field -->
                    <div class="lg:col-span-4">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Search Transaction</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i data-lucide="search" class="w-4 h-4 text-gray-400 group-focus-within:text-red-500 transition-colors"></i>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Enter Transaction ID (TRX-...)" 
                                   class="block w-full pl-11 h-12 bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all placeholder:text-gray-400">
                        </div>
                    </div>

                    <!-- Date Range Fields -->
                    <div class="lg:col-span-5">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Date Range</label>
                        <div class="flex flex-col sm:flex-row items-center gap-3">
                            <div class="relative flex-1 w-full">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
                                </div>
                                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                                       class="block w-full pl-11 h-12 bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all">
                            </div>
                            <div class="text-gray-300 font-bold text-xs uppercase hidden sm:block">to</div>
                            <div class="relative flex-1 w-full">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
                                </div>
                                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                                       class="block w-full pl-11 h-12 bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Voided By Filter -->
                    <div class="lg:col-span-3">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Authorized By</label>
                        <div class="flex items-center gap-2">
                            <select name="voided_by" class="block w-full h-12 bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all">
                                <option value="all">All Admins</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('voided_by') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="bg-red-600 text-white w-14 h-12 rounded-xl font-bold text-sm hover:bg-red-700 active:scale-[0.98] transition-all shadow-md shadow-red-200 flex items-center justify-center shrink-0">
                                <i data-lucide="search" class="w-5 h-5"></i>
                            </button>
                        </div>
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
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Time Voided</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Authorized By</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($voids as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div>{{ $item->voided_at ? \Carbon\Carbon::parse($item->voided_at)->format('M d, Y') : '-' }}</div>
                                <div class="text-xs text-red-500">{{ $item->voided_at ? \Carbon\Carbon::parse($item->voided_at)->format('h:i A') : '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                {{ $item->order->transaction_id ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                {{ $item->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                <span class="bg-red-100 text-red-800 px-2 py-0.5 rounded-full font-bold">
                                    {{ $item->quantity }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                ₱{{ number_format($item->unit_price * $item->quantity, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @php
                                    $authorizer = \App\Models\User::find($item->voided_by);
                                @endphp
                                <div class="flex items-center gap-2">
                                    <i data-lucide="shield-alert" class="w-4 h-4 text-red-500"></i>
                                    {{ $authorizer ? $authorizer->name : 'Unknown' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if($item->order)
                                    <a href="{{ route('admin.transactions.show', $item->order) }}" class="text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 px-2 py-1 rounded transition-colors inline-block">
                                        View Order
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i data-lucide="shield-ban" class="w-10 h-10 text-gray-300 mb-2"></i>
                                    <p>No voided items found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($voids->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $voids->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
