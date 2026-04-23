@extends('layouts.app')

@section('content')
<div class="p-4 md:p-8 max-w-4xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('admin.room_pricing.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-indigo-600 mb-4 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Back to Overview
        </a>
        <h1 class="text-3xl font-bold text-slate-900">Edit Pricing Rules</h1>
        <p class="text-slate-600">Configure tiered rates and overtime rules for rooms.</p>
    </div>

    <form action="{{ route('admin.room_pricing.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <!-- Section 1: Major Tiers -->
            <div class="p-6 md:p-8 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <i data-lucide="layers" class="w-5 h-5 text-indigo-600"></i>
                    Time Tiers (Standard Blocks)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="price_30_min" class="block text-sm font-bold text-slate-700 mb-2">30 Minutes Price (₱)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">₱</span>
                            <input type="number" step="0.01" name="price_30_min" id="price_30_min" 
                                   value="{{ old('price_30_min', $pricing->price_30_min ?? 100) }}"
                                   class="w-full pl-10 pr-4 py-4 bg-white border-2 border-slate-100 rounded-xl focus:border-indigo-500 outline-none transition-all text-xl font-bold">
                        </div>
                        @error('price_30_min') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="price_60_min" class="block text-sm font-bold text-slate-700 mb-2">1 Hour Price (₱)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">₱</span>
                            <input type="number" step="0.01" name="price_60_min" id="price_60_min" 
                                   value="{{ old('price_60_min', $pricing->price_60_min ?? 350) }}"
                                   class="w-full pl-10 pr-4 py-4 bg-white border-2 border-slate-100 rounded-xl focus:border-indigo-500 outline-none transition-all text-xl font-bold">
                        </div>
                        @error('price_60_min') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Overtime Rules -->
            <div class="p-6 md:p-8 space-y-6">
                <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <i data-lucide="timer" class="w-5 h-5 text-amber-600"></i>
                    Incremental / Overtime Rules
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="overtime_unit_minutes" class="block text-sm font-bold text-slate-700 mb-2">Every (Minutes)</label>
                        <select name="overtime_unit_minutes" id="overtime_unit_minutes" 
                                class="w-full px-4 py-4 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-indigo-500 focus:bg-white outline-none transition-all font-semibold">
                            <option value="5" {{ (old('overtime_unit_minutes', $pricing->overtime_unit_minutes ?? 10) == 5) ? 'selected' : '' }}>5 Minutes</option>
                            <option value="10" {{ (old('overtime_unit_minutes', $pricing->overtime_unit_minutes ?? 10) == 10) ? 'selected' : '' }}>10 Minutes</option>
                            <option value="15" {{ (old('overtime_unit_minutes', $pricing->overtime_unit_minutes ?? 10) == 15) ? 'selected' : '' }}>15 Minutes</option>
                        </select>
                        <p class="mt-2 text-xs text-slate-500">The unit used to calculate loose minutes between tiers.</p>
                        @error('overtime_unit_minutes') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="overtime_unit_price" class="block text-sm font-bold text-slate-700 mb-2">Incremental Price (₱)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">₱</span>
                            <input type="number" step="0.01" name="overtime_unit_price" id="overtime_unit_price" 
                                   value="{{ old('overtime_unit_price', $pricing->overtime_unit_price ?? 50) }}"
                                   class="w-full pl-10 pr-4 py-4 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-indigo-500 focus:bg-white outline-none transition-all font-bold">
                        </div>
                        @error('overtime_unit_price') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <label for="grace_period_minutes" class="block text-sm font-bold text-slate-700 mb-2">Global Grace Period (Minutes)</label>
                    <input type="number" name="grace_period_minutes" id="grace_period_minutes" 
                           value="{{ old('grace_period_minutes', $pricing->grace_period_minutes ?? 10) }}"
                           class="w-full max-w-xs px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-indigo-500 outline-none transition-all font-semibold">
                    <p class="mt-2 text-xs text-slate-500 italic">No charge if the session ends within this time from start.</p>
                </div>
            </div>

            <!-- Hidden / Legacy fields to keep model happy -->
            <input type="hidden" name="base_rate_per_hour" value="{{ $pricing->base_rate_per_hour ?? 350 }}">
            <input type="hidden" name="billing_unit_minutes" value="{{ $pricing->billing_unit_minutes ?? 30 }}">
            <input type="hidden" name="per_room_rate" value="1">

            <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-end">
                <button type="submit" 
                        class="px-8 py-4 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all shadow-lg hover:shadow-indigo-200 active:scale-95 font-bold flex items-center gap-2">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Update Pricing Rules
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
