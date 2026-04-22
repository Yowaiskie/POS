@extends('layouts.app')

@section('content')
<div class="flex h-screen flex-col lg:flex-row" x-data="{ 
    showCheckout: false,
    paymentMethod: 'cash',
    amountReceived: 0,
    total: {{ $activeOrderTotal }},
    get change() { return Math.max(0, this.amountReceived - this.total) }
}">
    <!-- Categories Sidebar -->
    <div class="w-full lg:w-48 bg-[--sidebar] border-b lg:border-b-0 lg:border-r border-[--border] p-4 shrink-0">
        <h2 class="text-sm text-[--muted-foreground] mb-4 px-2 hidden lg:block">Categories</h2>
        <div class="flex lg:flex-col gap-2 overflow-x-auto lg:overflow-x-visible">
            @foreach($categories as $cat)
                <a href="{{ route('orders.index', ['category' => $cat->slug]) }}" class="flex-shrink-0 lg:w-full p-3 md:p-4 rounded-lg text-left transition-all active:scale-95 font-medium {{ $selectedCategory === $cat->slug ? 'bg-[#6366f1] text-white shadow-md' : 'bg-white border-2 border-gray-200 hover:bg-gray-50' }}">
                    <div class="text-2xl mb-1">{{ $cat->icon }}</div>
                    <div class="text-xs md:text-sm">{{ $cat->name }}</div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Main Content: Product Grid -->
    <div class="flex-1 p-4 md:p-8 overflow-y-auto bg-slate-50">
        <div class="max-w-[1600px] mx-auto">
            <div class="mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-2">Short Orders</h1>
                <p class="text-slate-600">Quick point-of-sale for walk-in customers</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse($items as $item)
                    <form action="{{ route('orders.add-item') }}" method="POST" class="h-full">
                        @csrf
                        <input type="hidden" name="menu_item_id" value="{{ $item->id }}">
                        <button type="submit" class="w-full bg-white border border-slate-200 rounded-xl p-6 hover:border-indigo-400 hover:shadow-xl hover:-translate-y-1 transition-all duration-200 active:scale-98 flex flex-col gap-4 min-h-[150px] text-left" style="box-shadow: var(--shadow)">
                            <h3 class="font-semibold text-slate-900 leading-snug line-clamp-2 flex-1">
                                {{ $item->name }}
                            </h3>
                            <div class="text-left">
                                <div class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">₱{{ number_format($item->price) }}</div>
                            </div>
                        </button>
                    </form>
                @empty
                    <div class="col-span-full bg-white border border-slate-200 rounded-2xl p-6 text-center text-slate-500">
                        No items for this category yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Sidebar: Order Summary -->
    <div class="w-full lg:w-96 bg-[--sidebar] border-t lg:border-t-0 lg:border-l border-[--border] flex flex-col max-h-[50vh] lg:max-h-none">
        <div class="p-4 md:p-6 border-b border-[--border]">
            <div class="flex items-center gap-3 mb-2">
                <i data-lucide="shopping-cart" class="w-5 h-5 md:w-6 md:h-6 text-[--neon-violet]"></i>
                <h2 class="text-xl md:text-2xl font-semibold">Order Summary</h2>
            </div>
            <div class="text-sm text-[--muted-foreground]">
                {{ $activeOrderItemCount }} items selected
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 md:p-6 space-y-3">
            @forelse($activeOrderItems as $orderItem)
                <div class="bg-white border-2 border-gray-200 rounded-lg p-4 shadow-sm">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1 pr-2">
                            <div class="mb-1 font-semibold text-sm md:text-base">{{ $orderItem->name }}</div>
                            <div class="text-xs md:text-sm text-gray-500">₱{{ number_format($orderItem->unit_price) }} each</div>
                        </div>
                        <div class="text-base md:text-lg font-bold text-[#ec4899]">₱{{ number_format($orderItem->unit_price * $orderItem->quantity) }}</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <form action="{{ route('orders.update-quantity', $orderItem) }}" method="POST">
                            @csrf
                            <input type="hidden" name="delta" value="-1">
                            <button type="submit" class="w-9 h-9 flex items-center justify-center bg-gray-200 hover:bg-gray-300 active:scale-95 rounded-lg transition-all font-bold">
                                <i data-lucide="minus" class="w-4 h-4"></i>
                            </button>
                        </form>
                        <div class="flex-1 text-center font-bold text-lg">{{ $orderItem->quantity }}</div>
                        <form action="{{ route('orders.update-quantity', $orderItem) }}" method="POST">
                            @csrf
                            <input type="hidden" name="delta" value="1">
                            <button type="submit" class="w-9 h-9 flex items-center justify-center bg-[#6366f1] text-white hover:bg-[#5558e3] active:scale-95 rounded-lg transition-all font-bold">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center text-slate-400">
                    No items in the cart yet.
                </div>
            @endforelse
        </div>

        <div class="p-4 md:p-6 border-t border-gray-200 space-y-4 bg-gray-50">
            <div class="flex justify-between items-center text-xl md:text-2xl font-bold">
                <span>Total</span>
                <span class="text-[#ec4899]">₱{{ number_format($activeOrderTotal) }}</span>
            </div>

            <button @click="showCheckout = true" :disabled="total <= 0" class="w-full px-6 py-3.5 md:py-4 bg-[#10b981] text-white rounded-lg hover:bg-[#059669] active:scale-95 transition-all shadow-md font-medium text-lg disabled:opacity-50 disabled:cursor-not-allowed">
                Checkout
            </button>
            
            <form action="{{ route('orders.clear') }}" method="POST">
                @csrf
                <button type="submit" class="w-full px-6 py-2.5 md:py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 active:scale-95 transition-all flex items-center justify-center gap-2 font-medium">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    Clear Cart
                </button>
            </form>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" x-show="showCheckout" x-transition x-cloak>
        <div class="bg-white rounded-2xl p-6 md:p-8 max-w-md w-full relative shadow-2xl" @click.away="showCheckout = false">
            <button @click="showCheckout = false" class="absolute top-4 right-4 p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>

            <h2 class="text-2xl font-bold text-slate-900 mb-6">Payment Checkout</h2>

            <form action="{{ route('orders.checkout') }}" method="POST">
                @csrf
                <div class="mb-6 p-4 bg-indigo-50 rounded-xl border border-indigo-100">
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total Amount</div>
                    <div class="text-3xl font-black text-indigo-600">₱{{ number_format($activeOrderTotal) }}</div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">Payment Method</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="cash" x-model="paymentMethod" class="sr-only peer">
                                <div class="px-4 py-3 rounded-xl font-bold transition-all text-center border-2 border-slate-100 text-slate-500 peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 peer-checked:shadow-lg">Cash</div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="gcash" x-model="paymentMethod" class="sr-only peer">
                                <div class="px-4 py-3 rounded-xl font-bold transition-all text-center border-2 border-slate-100 text-slate-500 peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 peer-checked:shadow-lg">G-Cash</div>
                            </label>
                        </div>
                    </div>

                    <div x-show="paymentMethod === 'cash'" x-transition>
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-1">Amount Received</label>
                                <input type="number" name="amount_received" x-model.number="amountReceived" required class="w-full px-4 py-3 bg-slate-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-indigo-500 focus:outline-none transition-all font-black text-2xl text-slate-700">
                            </div>
                            <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-100 flex justify-between items-center">
                                <span class="text-xs font-bold text-emerald-600 uppercase tracking-widest">Change</span>
                                <span class="text-2xl font-black text-emerald-700">₱<span x-text="change.toLocaleString()"></span></span>
                            </div>
                        </div>
                    </div>

                    <div x-show="paymentMethod === 'gcash'" x-transition>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-1">Reference Number</label>
                            <input type="text" name="reference_number" :required="paymentMethod === 'gcash'" placeholder="Enter G-Cash Ref #" class="w-full px-4 py-3 bg-slate-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-indigo-500 focus:outline-none transition-all font-bold text-slate-700">
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 mt-8">
                    <button type="button" @click="showCheckout = false" class="flex-1 px-6 py-4 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 active:scale-95 transition-all font-bold">Cancel</button>
                    <button type="submit" :disabled="paymentMethod === 'cash' && (amountReceived < total || total <= 0)" class="flex-1 px-6 py-4 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 active:scale-95 transition-all font-black shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">Complete</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
