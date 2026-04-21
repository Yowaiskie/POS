@extends('layouts.app')

@section('content')
<div class="max-w-[1600px] mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-2">Reports & Analytics</h1>
        <p class="text-slate-600">Track performance, sales, and staff metrics</p>
    </div>

    <!-- Period Selector -->
    <div class="flex gap-3 mb-8">
        @foreach(['daily', 'weekly', 'monthly'] as $p)
            <button class="px-6 py-3 rounded-xl font-bold transition-all capitalize {{ $p === 'daily' ? 'bg-gradient-to-r from-indigo-600 to-indigo-500 text-white shadow-lg shadow-indigo-100' : 'bg-white border-2 border-slate-200 text-slate-700 hover:border-indigo-400' }}">
                {{ $p }}
            </button>
        @endforeach
    </div>

    <!-- Sales Overview -->
    <div class="mb-10">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-1.5 h-6 bg-gradient-to-b from-indigo-500 to-purple-500 rounded-full"></div>
            <h2 class="text-xl md:text-2xl font-bold text-slate-900 tracking-tight">Sales Overview</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-bold text-slate-400 uppercase tracking-wider">Total Sales</span>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center shadow-lg shadow-emerald-100">
                        <i data-lucide="philippine-peso" class="w-6 h-6 text-white"></i>
                    </div>
                </div>
                <div class="text-4xl font-black text-emerald-600 tracking-tighter">₱1,850</div>
                <div class="text-xs font-bold text-slate-400 mt-2 uppercase tracking-widest">daily total</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-bold text-slate-400 uppercase tracking-wider">Room Rental</span>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center shadow-lg shadow-blue-100">
                        <i data-lucide="package" class="w-6 h-6 text-white"></i>
                    </div>
                </div>
                <div class="text-4xl font-black text-blue-600 tracking-tighter">₱1,200</div>
                <div class="text-xs font-bold text-slate-400 mt-2 uppercase tracking-widest">65% of total</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-bold text-slate-400 uppercase tracking-wider">Short Orders</span>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-lg shadow-purple-100">
                        <i data-lucide="bar-chart-3" class="w-6 h-6 text-white"></i>
                    </div>
                </div>
                <div class="text-4xl font-black text-purple-600 tracking-tighter">₱650</div>
                <div class="text-xs font-bold text-slate-400 mt-2 uppercase tracking-widest">35% of total</div>
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
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b-2 border-slate-100">
                        <tr>
                            <th class="text-left px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Item</th>
                            <th class="text-center px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Qty</th>
                            <th class="text-right px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach([
                            ['name' => 'Cheesy Katsu', 'qty' => 245, 'rev' => 21805],
                            ['name' => 'Hungarian', 'qty' => 189, 'rev' => 19845],
                            ['name' => 'Hungarian + Tapa', 'qty' => 134, 'rev' => 20100]
                        ] as $index => $item)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 font-black text-xs">
                                            {{ $index + 1 }}
                                        </div>
                                        <span class="font-bold text-slate-700">{{ $item['name'] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-slate-500">{{ $item['qty'] }}</td>
                                <td class="px-6 py-4 text-right text-emerald-600 font-black">₱{{ number_format($item['rev']) }}</td>
                            </tr>
                        @endforeach
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
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b-2 border-slate-100">
                        <tr>
                            <th class="text-left px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Staff</th>
                            <th class="text-center px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Sales</th>
                            <th class="text-right px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Performance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach([
                            ['name' => 'John Doe', 'sales' => 18450, 'perf' => 'Top Performer'],
                            ['name' => 'Jane Smith', 'sales' => 21300, 'perf' => 'Rising Star']
                        ] as $staff)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-black text-xs shadow-md">
                                            {{ substr($staff['name'], 0, 1) }}
                                        </div>
                                        <span class="font-bold text-slate-700">{{ $staff['name'] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center font-black text-indigo-600">₱{{ number_format($staff['sales']) }}</td>
                                <td class="px-6 py-4 text-right">
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-wider border border-emerald-100">
                                        {{ $staff['perf'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
