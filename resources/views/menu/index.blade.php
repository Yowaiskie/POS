@extends('layouts.app')

@section('content')
<div class="p-4 md:p-8 max-w-[1600px] mx-auto" x-data="{ 
    showModal: false, 
    editingItem: null,
    formData: { name: '', price: '', category_id: '', stock_quantity: '', unlimited: true },
    openAddModal() {
        this.editingItem = null;
        this.formData = { name: '', price: '', category_id: '{{ $categories->first()?->id }}', stock_quantity: '', unlimited: true };
        this.showModal = true;
    },
    openEditModal(item) {
        this.editingItem = item;
        this.formData = {
            name: item.name,
            price: item.price,
            category_id: item.category_id,
            stock_quantity: item.stock_quantity ?? '',
            unlimited: item.stock_quantity === null
        };
        this.showModal = true;
    }
}">
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-2">Menu Management</h1>
                <p class="text-slate-600">Manage your products, packages, and pricing</p>
            </div>
            <button @click="openAddModal()" class="px-6 py-3 bg-[#10b981] text-white rounded-lg hover:bg-[#059669] active:scale-95 transition-all shadow-md font-medium flex items-center gap-2 w-full sm:w-auto justify-center">
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
            @php $status = $item->stockStatus(); @endphp
            <div class="bg-white rounded-xl border-2 border-slate-200 p-5 md:p-6 transition-all hover:shadow-lg hover:border-indigo-400">
                <h3 class="text-lg md:text-xl mb-3 font-semibold text-slate-900">{{ $item->name }}</h3>
                <div class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-3">₱{{ number_format($item->price) }}</div>
                <div class="text-xs md:text-sm text-slate-600 capitalize mb-3 bg-slate-100 px-3 py-1 rounded-full inline-block">
                    {{ $item->category?->name ?? 'Uncategorized' }}
                </div>
                {{-- Stock badge --}}
                <div class="mb-4">
                    @if($status === 'unlimited')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700">
                            <i data-lucide="infinity" class="w-3 h-3"></i> Unlimited Stock
                        </span>
                    @elseif($status === 'out_of_stock')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700">
                            <i data-lucide="x-circle" class="w-3 h-3"></i> Out of Stock
                        </span>
                    @elseif($status === 'low')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">
                            <i data-lucide="alert-triangle" class="w-3 h-3"></i> Low: {{ $item->stock_quantity }} left
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">
                            <i data-lucide="package" class="w-3 h-3"></i> {{ $item->stock_quantity }} in stock
                        </span>
                    @endif
                </div>
                <div class="flex gap-2 mt-4">
                    <button @click='openEditModal(@json($item))' class="flex-1 px-4 py-2 bg-[#6366f1] text-white rounded-lg hover:bg-[#5558e3] active:scale-95 transition-all shadow-sm text-sm font-medium">
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
        @empty
            <div class="bg-white rounded-xl border-2 border-dashed border-slate-200 p-6 text-center text-slate-500 col-span-full">
                No menu items available.
            </div>
        @endforelse
    </div>

    <!-- Add/Edit Modal -->
    <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" x-show="showModal" x-transition x-cloak>
        <div class="bg-white rounded-2xl p-6 md:p-8 max-w-md w-full relative shadow-2xl" @click.away="showModal = false">
            <button @click="showModal = false" class="absolute top-4 right-4 p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>

            <h2 class="text-2xl font-bold text-slate-900 mb-6" x-text="editingItem ? 'Edit Item' : 'Add New Item'"></h2>

            <form :action="editingItem ? `{{ url('menu') }}/${editingItem.id}` : '{{ route('menu.store') }}'" method="POST">
                @csrf
                <template x-if="editingItem">
                    @method('PUT')
                </template>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Item Name</label>
                        <input type="text" name="name" x-model="formData.name" required placeholder="Enter item name" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-indigo-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Price</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-600 font-semibold">₱</span>
                            <input type="number" name="price" x-model="formData.price" required placeholder="0" class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:border-indigo-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Category</label>
                        <select name="category_id" x-model="formData.category_id" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-indigo-500 focus:outline-none">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Stock Quantity --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Stock Quantity</label>
                        <div class="flex items-center gap-3 mb-2">
                            <label class="flex items-center gap-2 cursor-pointer select-none text-sm text-slate-600">
                                <input type="checkbox" name="unlimited" value="1"
                                       x-model="formData.unlimited"
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                Unlimited (no stock tracking)
                            </label>
                        </div>
                        <input type="number" name="stock_quantity"
                               x-model="formData.stock_quantity"
                               x-bind:disabled="formData.unlimited"
                               min="0"
                               placeholder="e.g. 50"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-indigo-500 focus:outline-none disabled:opacity-40 disabled:bg-slate-50">
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="button" @click="showModal = false" class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 active:scale-95 transition-all font-medium">Cancel</button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-[#6366f1] text-white rounded-lg hover:bg-[#5558e3] active:scale-95 transition-all shadow-md font-medium" x-text="editingItem ? 'Save Changes' : 'Add Item'"></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
