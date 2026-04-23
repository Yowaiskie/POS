@extends('layouts.app')

@section('content')
<div class="p-4 md:p-8 max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-2">Room Pricing</h1>
            <p class="text-slate-600">Configure tiered rates and automatic overtime rules.</p>
        </div>
        <a href="{{ route('admin.room_pricing.edit') }}" 
           class="flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all shadow-md hover:shadow-lg active:scale-95 font-semibold">
            <i data-lucide="edit-3" class="w-5 h-5"></i>
            Edit Rates
        </a>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3">
        <i data-lucide="check-circle" class="w-5 h-5"></i>
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- 30 Min Tier -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600 mb-4">
                <i data-lucide="clock-3" class="w-5 h-5"></i>
            </div>
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">30 Minutes</h3>
            <div class="text-3xl font-black text-slate-900">
                ₱{{ number_format($pricing->price_30_min ?? 100, 2) }}
            </div>
        </div>

        <!-- 1 Hour Tier -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600 mb-4">
                <i data-lucide="timer" class="w-5 h-5"></i>
            </div>
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">1 Hour</h3>
            <div class="text-3xl font-black text-slate-900">
                ₱{{ number_format($pricing->price_60_min ?? 350, 2) }}
            </div>
        </div>

        <!-- Overtime Rule -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm border-l-4 border-l-amber-400">
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center text-amber-600 mb-4">
                <i data-lucide="trending-up" class="w-5 h-5"></i>
            </div>
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Overtime Rate</h3>
            <div class="text-xl font-bold text-slate-900">
                ₱{{ number_format($pricing->overtime_unit_price ?? 50, 2) }}
                <span class="text-xs font-normal text-slate-500">per {{ $pricing->overtime_unit_minutes ?? 10 }}m</span>
            </div>
        </div>
    </div>

    <!-- Rule Explanation -->
    <div class="mt-10 bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="p-6 md:p-8 bg-slate-50 border-b border-slate-100">
            <h4 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="info" class="w-5 h-5 text-indigo-600"></i>
                Active Billing Logic
            </h4>
        </div>
        <div class="p-6 md:p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-6">
                    <div>
                        <div class="font-bold text-slate-800 mb-2">1. The "60-Min First" Rule</div>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            Every full hour is charged at the <strong>1-hour rate (₱{{ number_format($pricing->price_60_min ?? 350) }})</strong>.
                        </p>
                    </div>
                    <div>
                        <div class="font-bold text-slate-800 mb-2">2. The "30-Min Catch" Rule</div>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            After full hours, if remaining time is <strong>30 minutes or more</strong>, a flat <strong>₱{{ number_format($pricing->price_30_min ?? 100) }}</strong> is added.
                        </p>
                    </div>
                </div>
                <div class="space-y-6">
                    <div>
                        <div class="font-bold text-slate-800 mb-2">3. Overtime Increment</div>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            Loose minutes (less than 30m, or extra minutes after a 30m block) are charged at <strong>₱{{ number_format($pricing->overtime_unit_price ?? 50) }}</strong> per <strong>{{ $pricing->overtime_unit_minutes ?? 10 }} minutes</strong>.
                        </p>
                    </div>
                    <div>
                        <div class="font-bold text-slate-800 mb-2">4. Smart Capping</div>
                        <p class="text-sm text-slate-600 leading-relaxed text-indigo-600 italic">
                            The system automatically caps the overtime charge so it never exceeds the price of the next higher tier.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
