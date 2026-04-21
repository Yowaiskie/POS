@extends('layouts.app')

@section('content')
<div class="max-w-[1600px] mx-auto">
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-2">Menu Management</h1>
                <p class="text-slate-600">Manage your products, packages, and pricing</p>
            </div>
            <button class="px-6 py-3 bg-[#10b981] text-white rounded-lg hover:bg-[#059669] active:scale-95 transition-all shadow-md font-medium flex items-center gap-2 w-full sm:w-auto justify-center">
                <i data-lucide="plus" class="w-5 h-5"></i>
                Add Item
            </button>
        </div>
    </div>

    @php
        $categories = [
            ['id' => 'bowl-meal', 'name' => 'Bowl Meal', 'count' => 8],
            ['id' => 'silog-meal', 'name' => 'Silog Meal', 'count' => 8],
            ['id' => 'sizzling-plate', 'name' => 'Sizzling Plate', 'count' => 6],
            ['id' => 'combo-meal', 'name' => 'Combo Meal', 'count' => 9],
        ];

        $items = [
            ['id' => '1', 'name' => 'Cheesy Katsu', 'price' => 89, 'category' => 'bowl-meal'],
            ['id' => '2', 'name' => 'Cheesy Karaage', 'price' => 89, 'category' => 'bowl-meal'],
            ['id' => '3', 'name' => 'Mushroom Gravy', 'price' => 89, 'category' => 'bowl-meal'],
            ['id' => '4', 'name' => 'Creamy Salted Egg Chicken', 'price' => 105, 'category' => 'bowl-meal'],
            ['id' => '5', 'name' => 'Orange Chicken', 'price' => 89, 'category' => 'bowl-meal'],
            ['id' => '6', 'name' => 'Buffalo Pops', 'price' => 89, 'category' => 'bowl-meal'],
            ['id' => '7', 'name' => 'Sisig Popcorn Chicken', 'price' => 89, 'category' => 'bowl-meal'],
            ['id' => '8', 'name' => 'Chicken Skin', 'price' => 89, 'category' => 'bowl-meal'],
        ];

        $selectedCategory = 'bowl-meal';
    @endphp

    <div class="flex gap-2 md:gap-3 mb-6 md:mb-8 overflow-x-auto pb-2">
        @foreach($categories as $cat)
            <button class="flex-shrink-0 px-4 md:px-6 py-3 rounded-lg transition-all active:scale-95 font-medium {{ $selectedCategory === $cat['id'] ? 'bg-[#6366f1] text-white shadow-md' : 'bg-white border-2 border-gray-200 hover:border-[#6366f1] hover:shadow-sm' }}">
                <div class="mb-1 font-semibold text-left">{{ $cat['name'] }}</div>
                <div class="text-xs md:text-sm opacity-75 text-left">{{ $cat['count'] }} items</div>
            </button>
        @endforeach
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
        @foreach($items as $item)
            <div class="bg-white rounded-xl border-2 border-slate-200 p-5 md:p-6 transition-all hover:shadow-lg hover:border-indigo-400">
                <h3 class="text-lg md:text-xl mb-3 font-semibold text-slate-900">{{ $item['name'] }}</h3>
                <div class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4">₱{{ number_format($item['price']) }}</div>
                <div class="text-xs md:text-sm text-slate-600 capitalize mb-4 bg-slate-100 px-3 py-1 rounded-full inline-block">
                    {{ str_replace('-', ' ', $item['category']) }}
                </div>
                <div class="flex gap-2 mt-4">
                    <button class="flex-1 px-4 py-2 bg-[#6366f1] text-white rounded-lg hover:bg-[#5558e3] active:scale-95 transition-all shadow-sm text-sm font-medium">
                        Edit
                    </button>
                    <button class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 active:scale-95 transition-all text-sm font-medium flex items-center gap-1">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
