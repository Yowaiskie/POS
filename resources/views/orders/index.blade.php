@extends('layouts.app')

@section('content')
<div class="flex flex-col lg:flex-row -m-4 md:-m-8 h-[calc(100vh-64px)] lg:h-screen overflow-hidden">
    <!-- Categories Sidebar -->
    <div class="w-full lg:w-48 bg-white border-b lg:border-b-0 lg:border-r border-slate-200 p-4 shrink-0">
        <h2 class="text-sm text-slate-500 mb-4 px-2 hidden lg:block uppercase tracking-wider font-bold">Categories</h2>
        <div class="flex lg:flex-col gap-2 overflow-x-auto lg:overflow-x-visible pb-2 lg:pb-0">
            @php
                $categories = [
                    ['id' => 'bowl-meal', 'name' => 'Bowl Meal', 'icon' => '🍜'],
                    ['id' => 'silog-meal', 'name' => 'Silog Meal', 'icon' => '🍳'],
                    ['id' => 'sizzling-plate', 'name' => 'Sizzling Plate', 'icon' => '🔥'],
                    ['id' => 'combo-meal', 'name' => 'Combo Meal', 'icon' => '🍱'],
                ];
                $selectedCategory = 'bowl-meal';
            @endphp

            @foreach($categories as $cat)
                <button class="flex-shrink-0 lg:w-full p-3 md:p-4 rounded-xl text-left transition-all active:scale-95 font-medium {{ $selectedCategory === $cat['id'] ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white border-2 border-slate-100 hover:bg-slate-50' }}">
                    <div class="text-2xl mb-1">{{ $cat['icon'] }}</div>
                    <div class="text-xs md:text-sm">{{ $cat['name'] }}</div>
                </button>
            @endforeach
        </div>
    </div>

    <!-- Main Content: Product Grid -->
    <div class="flex-1 p-4 md:p-8 overflow-y-auto bg-slate-50/50">
        <div class="max-w-[1200px] mx-auto">
            <div class="mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-2">Short Orders</h1>
                <p class="text-slate-600 text-sm md:text-base">Quick point-of-sale for walk-in customers</p>
            </div>

            @php
                $items = [
                    ['id' => '1', 'name' => 'Cheesy Katsu', 'price' => 89],
                    ['id' => '2', 'name' => 'Cheesy Karaage', 'price' => 89],
                    ['id' => '3', 'name' => 'Mushroom Gravy', 'price' => 89],
                    ['id' => '4', 'name' => 'Creamy Salted Egg Chicken', 'price' => 105],
                    ['id' => '5', 'name' => 'Orange Chicken', 'price' => 89],
                    ['id' => '6', 'name' => 'Buffalo Pops', 'price' => 89],
                    ['id' => '7', 'name' => 'Sisig Popcorn Chicken', 'price' => 89],
                    ['id' => '8', 'name' => 'Chicken Skin', 'price' => 89],
                ];
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($items as $item)
                    <button class="bg-white border border-slate-200 rounded-2xl p-4 md:p-6 hover:border-indigo-400 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 active:scale-98 flex flex-col gap-4 min-h-[160px] text-left group">
                        <h3 class="font-bold text-slate-900 leading-snug line-clamp-2 flex-1 group-hover:text-indigo-600 transition-colors">
                            {{ $item['name'] }}
                        </h3>
                        <div>
                            <div class="text-2xl md:text-3xl font-black bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">₱{{ number_format($item['price']) }}</div>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Right Sidebar: Order Summary -->
    <div class="w-full lg:w-96 bg-white border-t lg:border-t-0 lg:border-l border-slate-200 flex flex-col shrink-0">
        <div class="p-4 md:p-6 border-b border-slate-200 bg-slate-50/50">
            <div class="flex items-center gap-3 mb-1">
                <i data-lucide="shopping-cart" class="w-6 h-6 text-indigo-600"></i>
                <h2 class="text-xl md:text-2xl font-bold text-slate-900 tracking-tight">Order Summary</h2>
            </div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                2 items selected
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 md:p-6 space-y-4">
            <!-- Sample Cart Items -->
            <div class="bg-white border-2 border-slate-100 rounded-2xl p-4 shadow-sm hover:border-indigo-200 transition-colors">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1 pr-2">
                        <div class="font-bold text-slate-900 mb-1">Cheesy Katsu</div>
                        <div class="text-xs font-semibold text-slate-400">₱89 each</div>
                    </div>
                    <div class="text-lg font-black text-rose-500">₱89</div>
                </div>
                <div class="flex items-center gap-3">
                    <button class="w-10 h-10 flex items-center justify-center bg-slate-100 hover:bg-slate-200 active:scale-90 rounded-xl transition-all font-bold text-slate-600">
                        <i data-lucide="minus" class="w-4 h-4"></i>
                    </button>
                    <div class="flex-1 text-center font-black text-xl text-slate-900">1</div>
                    <button class="w-10 h-10 flex items-center justify-center bg-indigo-600 text-white hover:bg-indigo-700 active:scale-90 rounded-xl transition-all shadow-md shadow-indigo-100">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <div class="bg-white border-2 border-slate-100 rounded-2xl p-4 shadow-sm hover:border-indigo-200 transition-colors">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1 pr-2">
                        <div class="font-bold text-slate-900 mb-1">Hungarian</div>
                        <div class="text-xs font-semibold text-slate-400">₱105 each</div>
                    </div>
                    <div class="text-lg font-black text-rose-500">₱105</div>
                </div>
                <div class="flex items-center gap-3">
                    <button class="w-10 h-10 flex items-center justify-center bg-slate-100 hover:bg-slate-200 active:scale-90 rounded-xl transition-all font-bold text-slate-600">
                        <i data-lucide="minus" class="w-4 h-4"></i>
                    </button>
                    <div class="flex-1 text-center font-black text-xl text-slate-900">1</div>
                    <button class="w-10 h-10 flex items-center justify-center bg-indigo-600 text-white hover:bg-indigo-700 active:scale-90 rounded-xl transition-all shadow-md shadow-indigo-100">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="p-4 md:p-6 border-t border-slate-200 space-y-4 bg-slate-50/80">
            <div class="flex justify-between items-center text-2xl font-black text-slate-900">
                <span>Total</span>
                <span class="text-rose-500">₱194</span>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Payment Method</label>
                <div class="grid grid-cols-2 gap-2">
                    <button class="px-4 py-3 rounded-xl font-bold transition-all active:scale-95 bg-indigo-600 text-white shadow-lg shadow-indigo-100">
                        Cash
                    </button>
                    <button class="px-4 py-3 rounded-xl font-bold transition-all active:scale-95 bg-white border-2 border-slate-200 text-slate-600 hover:border-indigo-400">
                        G-Cash
                    </button>
                </div>
            </div>

            <button class="w-full px-6 py-4 bg-emerald-500 text-white rounded-2xl hover:bg-emerald-600 active:scale-95 transition-all shadow-xl shadow-emerald-100 font-black text-lg">
                Checkout
            </button>
            <button class="w-full px-6 py-3 bg-white border-2 border-slate-200 text-slate-400 rounded-xl hover:text-rose-500 hover:border-rose-200 active:scale-95 transition-all flex items-center justify-center gap-2 font-bold text-sm">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
                Clear Cart
            </button>
        </div>
    </div>
</div>
@endsection
