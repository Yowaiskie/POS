@extends('layouts.app')

@section('content')
<div class="p-4 lg:p-8 max-w-4xl mx-auto">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <i data-lucide="file-text" class="w-8 h-8 text-indigo-600"></i>
                End of Shift Report
            </h1>
            <p class="text-sm text-gray-500 mt-1">Shift #{{ $shift->id }} • {{ $shift->created_at->format('M d, Y') }}</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-xl font-bold transition-colors shadow-sm flex items-center gap-2">
                <i data-lucide="printer" class="w-4 h-4"></i> Print Report
            </button>
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}" class="px-4 py-2.5 bg-indigo-600 text-white hover:bg-indigo-700 rounded-xl font-bold transition-colors shadow-sm flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Back
            </a>
        </div>
    </div>

    {{-- Status Banner --}}
    @if($shift->difference_type === 'matched')
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-4 shadow-sm print:border-black print:bg-white">
            <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center shrink-0">
                <i data-lucide="check-circle-2" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-emerald-800 font-bold text-lg">Perfect Match</h3>
                <p class="text-emerald-600 text-sm font-medium">Physical cash and GCash balances perfectly matched expectations.</p>
            </div>
        </div>
    @elseif($shift->difference_type === 'over')
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-2xl flex items-center gap-4 shadow-sm print:border-black print:bg-white">
            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center shrink-0">
                <i data-lucide="trending-up" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-blue-800 font-bold text-lg">Total Variance Over by ₱{{ number_format($shift->difference_amount, 2) }}</h3>
                <p class="text-blue-600 text-sm font-medium">Actual totals exceeded expected system amounts.</p>
            </div>
        </div>
    @elseif($shift->difference_type === 'short')
        <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-center gap-4 shadow-sm print:border-black print:bg-white">
            <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center shrink-0">
                <i data-lucide="trending-down" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-rose-800 font-bold text-lg">Total Variance Short by ₱{{ number_format(abs($shift->difference_amount), 2) }}</h3>
                <p class="text-rose-600 text-sm font-medium">Actual totals were less than expected system amounts.</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 print:grid-cols-1 print:gap-4">
        {{-- Shift Details --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex items-center gap-3">
                <i data-lucide="info" class="text-gray-400 w-5 h-5"></i>
                <h3 class="font-bold text-gray-800">Shift Details</h3>
            </div>
            <div class="p-0">
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-100">
                        <tr>
                            <td class="py-3 px-4 text-gray-500 font-medium">Cashier</td>
                            <td class="py-3 px-4 font-semibold text-right text-gray-900">{{ $shift->user->name }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 text-gray-500 font-medium">Started At</td>
                            <td class="py-3 px-4 font-semibold text-right text-gray-900">{{ $shift->start_time->format('M d, h:i A') }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 text-gray-500 font-medium">Ended At</td>
                            <td class="py-3 px-4 font-semibold text-right text-gray-900">{{ $shift->end_time ? $shift->end_time->format('M d, h:i A') : 'Ongoing' }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 text-gray-500 font-medium">Status</td>
                            <td class="py-3 px-4 font-semibold text-right text-gray-900 capitalize">
                                <span class="px-2 py-1 bg-gray-100 rounded-md">{{ str_replace('_', ' ', $shift->status) }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Sales Breakdown --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex items-center gap-3">
                <i data-lucide="pie-chart" class="text-gray-400 w-5 h-5"></i>
                <h3 class="font-bold text-gray-800">Sales Breakdown</h3>
            </div>
            <div class="p-0">
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-100">
                        <tr>
                            <td class="py-3 px-4 text-gray-500 font-medium">Room Sales</td>
                            <td class="py-3 px-4 font-semibold text-right text-gray-900 font-mono">₱{{ number_format($totals['room_sales'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 text-gray-500 font-medium">Short Order Sales</td>
                            <td class="py-3 px-4 font-semibold text-right text-gray-900 font-mono">₱{{ number_format($totals['short_sales'], 2) }}</td>
                        </tr>
                        <tr class="bg-indigo-50/30">
                            <td class="py-3 px-4 text-indigo-700 font-bold">Total Gross Sales</td>
                            <td class="py-3 px-4 font-bold text-right text-indigo-700 font-mono">₱{{ number_format($totals['room_sales'] + $totals['short_sales'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 text-gray-500 font-medium">Paid via GCash</td>
                            <td class="py-3 px-4 font-semibold text-right text-blue-600 font-mono">₱{{ number_format($totals['gcash_sales'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 text-gray-500 font-medium">Paid via Cash</td>
                            <td class="py-3 px-4 font-semibold text-right text-emerald-600 font-mono">₱{{ number_format($totals['cash_sales'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Reconciliation --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden lg:col-span-2">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex items-center gap-3">
                <i data-lucide="calculator" class="text-gray-400 w-5 h-5"></i>
                <h3 class="font-bold text-gray-800">Drawer & Digital Reconciliation</h3>
            </div>
            <div class="p-0">
                <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-100">
                    {{-- Cash Column --}}
                    <div>
                        <div class="px-4 py-3 bg-emerald-50/50 border-b border-gray-100 font-semibold text-emerald-800 flex items-center gap-2">
                            <i data-lucide="banknote" class="w-4 h-4"></i> Physical Cash
                        </div>
                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-gray-50">
                                <tr>
                                    <td class="py-3 px-4 text-gray-500 font-medium">Starting Float</td>
                                    <td class="py-3 px-4 font-semibold text-right text-gray-900 font-mono">₱{{ number_format($shift->starting_cash, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4 text-gray-500 font-medium">+ Cash Sales</td>
                                    <td class="py-3 px-4 font-semibold text-right text-emerald-600 font-mono">₱{{ number_format($totals['cash_sales'], 2) }}</td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="py-3 px-4 text-gray-700 font-bold text-xs uppercase tracking-wide">Expected Cash</td>
                                    <td class="py-3 px-4 font-bold text-right text-gray-900 font-mono">₱{{ number_format($shift->expected_cash, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4 text-gray-700 font-bold text-xs uppercase tracking-wide">Actual Cash Counted</td>
                                    <td class="py-3 px-4 font-bold text-right text-gray-900 font-mono">₱{{ number_format($shift->actual_cash, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4 font-bold text-xs uppercase tracking-wide {{ ($shift->actual_cash - $shift->expected_cash) >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">Cash Variance</td>
                                    <td class="py-3 px-4 font-bold text-right font-mono {{ ($shift->actual_cash - $shift->expected_cash) >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ ($shift->actual_cash - $shift->expected_cash) > 0 ? '+' : '' }}₱{{ number_format($shift->actual_cash - $shift->expected_cash, 2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- GCash Column --}}
                    <div>
                        <div class="px-4 py-3 bg-blue-50/50 border-b border-gray-100 font-semibold text-blue-800 flex items-center gap-2">
                            <i data-lucide="smartphone" class="w-4 h-4"></i> GCash (Digital)
                        </div>
                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-gray-50">
                                <tr>
                                    <td class="py-3 px-4 text-gray-500 font-medium">Starting Float</td>
                                    <td class="py-3 px-4 font-semibold text-right text-gray-900 font-mono">₱0.00</td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4 text-gray-500 font-medium">+ GCash Sales</td>
                                    <td class="py-3 px-4 font-semibold text-right text-blue-600 font-mono">₱{{ number_format($totals['gcash_sales'], 2) }}</td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="py-3 px-4 text-gray-700 font-bold text-xs uppercase tracking-wide">Expected GCash</td>
                                    <td class="py-3 px-4 font-bold text-right text-gray-900 font-mono">₱{{ number_format($shift->expected_gcash, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4 text-gray-700 font-bold text-xs uppercase tracking-wide">Actual GCash Counted</td>
                                    <td class="py-3 px-4 font-bold text-right text-gray-900 font-mono">₱{{ number_format($shift->actual_gcash, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-4 font-bold text-xs uppercase tracking-wide {{ ($shift->actual_gcash - $shift->expected_gcash) >= 0 ? 'text-blue-600' : 'text-rose-600' }}">GCash Variance</td>
                                    <td class="py-3 px-4 font-bold text-right font-mono {{ ($shift->actual_gcash - $shift->expected_gcash) >= 0 ? 'text-blue-600' : 'text-rose-600' }}">
                                        {{ ($shift->actual_gcash - $shift->expected_gcash) > 0 ? '+' : '' }}₱{{ number_format($shift->actual_gcash - $shift->expected_gcash, 2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Total Variance Footer --}}
                <div class="p-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center {{ $shift->difference_type === 'matched' ? 'bg-emerald-50 text-emerald-800' : ($shift->difference_type === 'over' ? 'bg-blue-50 text-blue-800' : 'bg-rose-50 text-rose-800') }}">
                    <span class="font-bold uppercase tracking-wide">Total Net Variance ({{ ucfirst($shift->difference_type) }})</span>
                    <span class="font-bold text-xl font-mono">
                        {{ $shift->difference_amount > 0 ? '+' : '' }}₱{{ number_format($shift->difference_amount, 2) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Voids & Audits --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden lg:col-span-2">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <i data-lucide="shield-alert" class="text-gray-400 w-5 h-5"></i>
                    <h3 class="font-bold text-gray-800">Voids & Audits</h3>
                </div>
                <span class="px-2.5 py-1 bg-red-100 text-red-700 rounded-md text-xs font-bold">{{ $totals['voids_count'] }} Items</span>
            </div>
            <div class="p-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-600">Total Void Value:</span>
                    <span class="text-sm font-bold text-red-600 font-mono">₱{{ number_format($totals['voids_total'], 2) }}</span>
                </div>
                <p class="text-xs text-gray-500">Detailed void report is available in the admin dashboard.</p>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .max-w-4xl, .max-w-4xl * {
            visibility: visible;
        }
        .max-w-4xl {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 0 !important;
        }
        button, a {
            display: none !important;
        }
        .shadow-sm {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
    }
</style>
@endsection
