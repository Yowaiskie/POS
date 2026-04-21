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

    <div class="flex gap-2 md:gap-3 mb-6 md:mb-8 overflow-x-auto pb-2">
        @foreach($categories as $cat)
            <a href="{{ route('menu.index', ['category' => $cat->slug]) }}" class="flex-shrink-0 px-4 md:px-6 py-3 rounded-lg transition-all active:scale-95 font-medium {{ $selectedCategory === $cat->slug ? 'bg-[#6366f1] text-white shadow-md' : 'bg-white border-2 border-gray-200 hover:border-[#6366f1] hover:shadow-sm' }}">
                <div class="mb-1 font-semibold text-left">{{ $cat->name }}</div>
                <div class="text-xs md:text-sm opacity-75 text-left">{{ $cat->items_count }} items</div>
            </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
        @forelse($items as $item)
            <div class="bg-white rounded-xl border-2 border-slate-200 p-5 md:p-6 transition-all hover:shadow-lg hover:border-indigo-400">
                <h3 class="text-lg md:text-xl mb-3 font-semibold text-slate-900">{{ $item->name }}</h3>
                <div class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4">₱{{ number_format($item->price) }}</div>
                <div class="text-xs md:text-sm text-slate-600 capitalize mb-4 bg-slate-100 px-3 py-1 rounded-full inline-block">
                    {{ $item->category?->name ?? 'Uncategorized' }}
                </div>
                <div class="flex gap-2 mt-4">
                    <button class="flex-1 px-4 py-2 bg-[#6366f1] text-white rounded-lg hover:bg-[#5558e3] active:scale-95 transition-all shadow-sm text-sm font-medium">
                        Edit
                    </button>
                    <form action="{{ route('menu.destroy', $item) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 active:scale-95 transition-all text-sm font-medium flex items-center gap-1">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty            <div class="bg-white rounded-xl border-2 border-dashed border-slate-200 p-6 text-center text-slate-500 col-span-full">
                No menu items available.
            </div>
        @endforelse
    </div>
</div>
@endsection
