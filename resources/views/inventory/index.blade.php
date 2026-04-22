@extends('layouts.app')

@section('content')
<div class="p-4 md:p-8 max-w-[1600px] mx-auto">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-2">Inventory</h1>
        <p class="text-slate-600">Manage stock levels for all menu items</p>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-5 py-4 shadow-sm">
            <i data-lucide="check-circle-2" class="w-5 h-5 shrink-0 text-emerald-500"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-5 py-4 shadow-sm">
            <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 text-red-500"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">
                <i data-lucide="package" class="w-6 h-6 text-indigo-600"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-slate-900">{{ $stats['total'] }}</div>
                <div class="text-sm text-slate-500">Total Items</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                <i data-lucide="check-circle-2" class="w-6 h-6 text-emerald-600"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-slate-900">{{ $stats['in_stock'] }}</div>
                <div class="text-sm text-slate-500">In Stock</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-amber-600"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-slate-900">{{ $stats['low_stock'] }}</div>
                <div class="text-sm text-slate-500">Low Stock</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
                <i data-lucide="x-circle" class="w-6 h-6 text-red-600"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-slate-900">{{ $stats['out_of_stock'] }}</div>
                <div class="text-sm text-slate-500">Out of Stock</div>
            </div>
        </div>
    </div>

    {{-- Category Filter --}}
    <div class="flex gap-2 md:gap-3 mb-6 overflow-x-auto pb-2">
        <a href="{{ route('inventory.index', ['category' => 'all']) }}"
           class="flex-shrink-0 px-4 md:px-5 py-2.5 rounded-lg transition-all font-medium text-sm {{ $selectedCategory === 'all' ? 'bg-[#6366f1] text-white shadow-md' : 'bg-white border-2 border-gray-200 hover:border-[#6366f1]' }}">
            All Items
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('inventory.index', ['category' => $cat->slug]) }}"
               class="flex-shrink-0 px-4 md:px-5 py-2.5 rounded-lg transition-all font-medium text-sm {{ $selectedCategory === $cat->slug ? 'bg-[#6366f1] text-white shadow-md' : 'bg-white border-2 border-gray-200 hover:border-[#6366f1]' }}">
                {{ $cat->name }}
            </a>
        @endforeach
    </div>

    {{-- Inventory Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="text-left px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Item</th>
                    <th class="text-left px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider hidden md:table-cell">Category</th>
                    <th class="text-left px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider hidden lg:table-cell">Price</th>
                    <th class="text-center px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="text-right px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Stock / Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($items as $item)
                    @php
                        $status = $item->stockStatus();
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors" x-data="{
                        editing: false,
                        unlimited: {{ $item->stock_quantity === null ? 'true' : 'false' }},
                        qty: {{ $item->stock_quantity ?? 0 }}
                    }">
                        <td class="px-5 py-4">
                            <div class="font-semibold text-slate-900">{{ $item->name }}</div>
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell text-slate-500">
                            {{ $item->category?->name ?? '—' }}
                        </td>
                        <td class="px-5 py-4 hidden lg:table-cell font-semibold text-slate-700">
                            ₱{{ number_format($item->price) }}
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($status === 'unlimited')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                                    <i data-lucide="infinity" class="w-3 h-3"></i> Unlimited
                                </span>
                            @elseif($status === 'out_of_stock')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                    <i data-lucide="x-circle" class="w-3 h-3"></i> Out of Stock
                                </span>
                            @elseif($status === 'low')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                    <i data-lucide="alert-triangle" class="w-3 h-3"></i> Low ({{ $item->stock_quantity }})
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                    <i data-lucide="check-circle-2" class="w-3 h-3"></i> {{ $item->stock_quantity }}
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            {{-- View mode --}}
                            <div class="flex items-center justify-end gap-2" x-show="!editing">
                                <button @click="editing = true"
                                    class="px-3 py-1.5 text-xs font-semibold bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors flex items-center gap-1.5">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit Stock
                                </button>
                            </div>

                            {{-- Edit mode --}}
                            <form action="{{ route('inventory.update', $item) }}" method="POST"
                                  class="flex items-center justify-end gap-2" x-show="editing" x-cloak>
                                @csrf
                                @method('PUT')

                                <label class="flex items-center gap-1.5 text-xs text-slate-600 cursor-pointer select-none">
                                    <input type="checkbox" name="unlimited" value="1"
                                           x-model="unlimited"
                                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    Unlimited
                                </label>

                                <input type="number" name="stock_quantity"
                                       x-model="qty"
                                       x-bind:disabled="unlimited"
                                       min="0"
                                       class="w-20 px-2 py-1.5 text-xs border-2 border-gray-200 rounded-lg focus:border-indigo-400 focus:outline-none disabled:opacity-40 disabled:bg-slate-100">

                                <button type="submit"
                                    class="px-3 py-1.5 text-xs font-semibold bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors flex items-center gap-1">
                                    <i data-lucide="save" class="w-3.5 h-3.5"></i> Save
                                </button>
                                <button type="button" @click="editing = false"
                                    class="px-3 py-1.5 text-xs font-semibold bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors">
                                    Cancel
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-slate-400">
                            No items found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
