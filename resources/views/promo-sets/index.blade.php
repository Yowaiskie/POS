@extends('layouts.app')

@section('content')
<div class="p-4 md:p-8 max-w-[1600px] mx-auto" x-data="{
    showCreateModal: false,
    showEditModal: false,
    activePromo: null,
    
    openEditModal(promo) {
        // Deep copy to avoid modifying the original data
        this.activePromo = JSON.parse(JSON.stringify(promo));
        // Ensure items structure is correct for the template
        if (this.activePromo.items) {
            this.activePromo.items = this.activePromo.items.map(i => ({
                menu_item_id: i.menu_item_id,
                quantity: i.quantity
            }));
        } else {
            this.activePromo.items = [];
        }
        this.showEditModal = true;
    }
}">
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-2">Promo Sets</h1>
                <p class="text-slate-600">Manage promotional packages and room inclusions</p>
            </div>
            <button @click="showCreateModal = true" class="px-6 py-3 bg-[#10b981] text-white rounded-lg hover:bg-[#059669] active:scale-95 transition-all shadow-md font-medium flex items-center gap-2 w-full sm:w-auto justify-center">
                <i data-lucide="plus" class="w-5 h-5"></i>
                Create New Set
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 text-emerald-700 p-4 rounded-xl border border-emerald-200 flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 bg-red-50 text-red-700 p-4 rounded-xl border border-red-200 flex items-center gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-500"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
        @foreach($promoSets as $promo)
            <div class="bg-white rounded-xl border-2 border-slate-200 p-5 md:p-6 transition-all hover:shadow-lg hover:border-indigo-400 flex flex-col">
                <!-- Header -->
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-lg md:text-xl font-semibold text-slate-900">{{ $promo->name }}</h3>
                        <span class="px-2 py-0.5 {{ $promo->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }} text-[10px] font-bold rounded uppercase">
                            {{ $promo->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="text-xs md:text-sm text-slate-500 font-medium bg-slate-100 px-3 py-1 rounded-full inline-block">
                        {{ $promo->duration_hours }} hrs Room Use
                    </div>
                </div>

                <!-- Price -->
                <div class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4">
                    ₱{{ number_format($promo->price) }}
                </div>

                <!-- Inclusions -->
                <div class="flex-1 mb-6">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3">Inclusions</div>
                    <ul class="space-y-2">
                        @foreach($promo->items as $item)
                            <li class="flex items-start gap-2 text-sm text-slate-600">
                                <i data-lucide="check" class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5"></i>
                                <span class="break-words"><span class="font-semibold">{{ $item->quantity }}x</span> {{ $item->menuItem->name ?? 'Unknown Item' }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Actions -->
                <div class="flex gap-2 mt-auto pt-4 border-t border-slate-100">
                    <button @click="openEditModal(@js($promo))" class="flex-1 px-4 py-2 bg-[#6366f1] text-white rounded-lg hover:bg-[#5558e3] active:scale-95 transition-all shadow-sm text-sm font-medium">
                        Edit
                    </button>
                    <form action="{{ route('promo-sets.destroy', $promo) }}" method="POST" onsubmit="return confirm('Are you sure?')" class="shrink-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 active:scale-95 transition-all text-sm font-medium flex items-center gap-1">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $promoSets->links() }}
    </div>

    @include('promo-sets.partials.create-modal')
    @include('promo-sets.partials.edit-modal')
</div>


@endsection
