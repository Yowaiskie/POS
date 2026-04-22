@extends('layouts.app')

@section('content')
<div class="p-4 md:p-8 max-w-[1600px] mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-2">Reports & Analytics</h1>
        <p class="text-slate-600">Track performance, sales, and staff metrics</p>
    </div>

    <!-- Period Selector -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div class="flex gap-3">
            @foreach(['daily', 'weekly', 'monthly'] as $p)
                <a href="{{ route('reports.index', ['period' => $p]) }}" class="px-6 py-3 rounded-lg font-semibold transition-all capitalize {{ $p === $period ? 'bg-gradient-to-r from-indigo-600 to-indigo-500 text-white shadow-lg' : 'bg-white border-2 border-slate-200 text-slate-700 hover:border-indigo-400' }}">
                    {{ $p }}
                </a>
            @endforeach
        </div>

        <a href="{{ route('reports.pdf', ['period' => $period]) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 text-white rounded-lg font-bold hover:bg-slate-800 transition-all shadow-lg">
            <i data-lucide="file-down" class="w-5 h-5"></i>
            Download PDF Report
        </a>
    </div>

    <!-- Sales Overview -->
    <div class="mb-10">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-1.5 h-6 bg-gradient-to-b from-indigo-500 to-purple-500 rounded-full"></div>
            <h2 class="text-xl md:text-2xl font-bold text-slate-900 tracking-tight">Sales Overview</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white border border-slate-200 rounded-xl p-6 hover:shadow-xl transition-all duration-200" style="box-shadow: var(--shadow-md)">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-medium text-slate-600">Total Sales</span>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center">
                        <i data-lucide="philippine-peso" class="w-6 h-6 text-white"></i>
                    </div>
                </div>
                <div class="text-4xl font-bold text-emerald-600">₱{{ number_format($totalSales) }}</div>
                <div class="text-xs text-slate-500 mt-2 capitalize">{{ $period }} total</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-6 hover:shadow-xl transition-all duration-200" style="box-shadow: var(--shadow-md)">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-medium text-slate-600">Room Rental</span>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center">
                        <i data-lucide="package" class="w-6 h-6 text-white"></i>
                    </div>
                </div>
                <div class="text-4xl font-bold text-blue-600">₱{{ number_format($roomSales) }}</div>
                <div class="text-xs text-slate-500 mt-2">{{ $roomPercent }}% of total</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-6 hover:shadow-xl transition-all duration-200" style="box-shadow: var(--shadow-md)">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-medium text-slate-600">Short Orders</span>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                        <i data-lucide="bar-chart-3" class="w-6 h-6 text-white"></i>
                    </div>
                </div>
                <div class="text-4xl font-bold text-purple-600">₱{{ number_format($shortSales) }}</div>
                <div class="text-xs text-slate-500 mt-2">{{ $shortPercent }}% of total</div>
            </div>
        </div>
    </div>

    <!-- Product Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
        <!-- Top Selling -->
        <div>
            <div class="flex items-center gap-3 mb-6">
                <div class="w-1.5 h-6 bg-gradient-to-b from-emerald-500 to-teal-500 rounded-full"></div>
                <h2 class="text-xl md:text-2xl font-bold text-slate-900 tracking-tight">Top Selling Items</h2>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden" style="box-shadow: var(--shadow-md)">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-emerald-50 to-teal-50 border-b-2 border-slate-200">
                        <tr>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-slate-700">Item</th>
                            <th class="text-center px-6 py-4 text-sm font-semibold text-slate-700">Qty</th>
                            <th class="text-right px-6 py-4 text-sm font-semibold text-slate-700">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($topSelling as $index => $item)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white font-bold text-sm">
                                            {{ $index + 1 }}
                                        </div>
                                        <span class="font-medium text-slate-900">{{ $item['name'] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center text-slate-600 font-semibold">{{ $item['qty'] }}</td>
                                <td class="px-6 py-4 text-right text-emerald-600 font-bold">₱{{ number_format($item['rev']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-4 text-center text-slate-400" colspan="3">No sales yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Staff Performance -->
        <div>
            <div class="flex items-center gap-3 mb-6">
                <div class="w-1.5 h-6 bg-gradient-to-b from-purple-500 to-pink-500 rounded-full"></div>
                <h2 class="text-xl md:text-2xl font-bold text-slate-900 tracking-tight">Staff Performance</h2>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden" style="box-shadow: var(--shadow-md)">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-purple-50 to-pink-50 border-b-2 border-slate-200">
                        <tr>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-slate-700">Staff</th>
                            <th class="text-center px-6 py-4 text-sm font-semibold text-slate-700">Sales</th>
                            <th class="text-right px-6 py-4 text-sm font-semibold text-slate-700">Performance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($staffPerformance as $staff)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white font-bold text-sm">
                                            {{ substr($staff['name'], 0, 1) }}
                                        </div>
                                        <span class="font-semibold text-slate-900">{{ $staff['name'] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center font-semibold text-slate-700">₱{{ number_format($staff['sales']) }}</td>
                                <td class="px-6 py-4 text-right">
                                    <span class="inline-flex items-center gap-1 text-emerald-600 text-sm font-semibold">
                                        {{ $staff['perf'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-4 text-center text-slate-400" colspan="3">No staff sales yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="mb-10">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-1.5 h-6 bg-gradient-to-b from-indigo-500 to-purple-500 rounded-full"></div>
            <h2 class="text-xl md:text-2xl font-bold text-slate-900 tracking-tight">Recent Transactions</h2>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden" style="box-shadow: var(--shadow-md)">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-slate-700">POS Transaction ID</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-slate-700">Type / Promo</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-slate-700">Payment</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-slate-700">Staff Name</th>
                        <th class="text-center px-6 py-4 text-sm font-semibold text-slate-700">Date & Time</th>
                        <th class="text-right px-6 py-4 text-sm font-semibold text-slate-700">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm font-semibold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-md">
                                    {{ $order->transaction_id ?? $order->order_number ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($order->promo_name)
                                    <span class="inline-flex items-center gap-1 text-sm font-bold text-emerald-600">
                                        <i data-lucide="sparkles" class="w-4 h-4"></i>
                                        {{ $order->promo_name }}
                                    </span>
                                @else
                                    <span class="text-sm font-medium text-slate-600 capitalize">
                                        {{ $order->order_type }} Order
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($order->payment_method === 'gcash')
                                    <div class="flex flex-col gap-1">
                                        <span class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-full w-fit">
                                            <i data-lucide="smartphone" class="w-3 h-3"></i>
                                            GCash
                                        </span>
                                        @if($order->reference_number)
                                            <span class="text-xs font-mono font-bold text-slate-600">Ref: {{ $order->reference_number }}</span>
                                        @else
                                            <span class="text-[10px] font-mono text-slate-400 italic">No Ref recorded</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                                        <i data-lucide="banknote" class="w-3 h-3"></i>
                                        Cash
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-600">
                                        {{ substr($order->user->name ?? 'S', 0, 1) }}
                                    </div>
                                    <span class="text-sm font-semibold text-slate-700">{{ $order->user->name ?? 'System' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-slate-500">
                                {{ $order->closed_at ? $order->closed_at->format('M d, Y h:i A') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-slate-900">
                                ₱{{ number_format($order->total_amount) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-4 text-center text-slate-400" colspan="4">No transactions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
            {{ $recentOrders->links() }}
        </div>
    </div>
</div>
@endsection
