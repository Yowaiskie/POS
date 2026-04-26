@extends('layouts.app')

@section('content')
@php
    $cartData = $activeOrderItems->map(fn($i) => [
        'id' => $i->id,
        'name' => $i->name,
        'quantity' => $i->quantity,
        'unit_price' => (float)$i->unit_price,
        'total' => (float)($i->unit_price * $i->quantity),
        'menu_item_id' => $i->menu_item_id,
        'stock_quantity' => $i->menuItem?->stock_quantity,
        'is_stock_deducted' => $i->is_stock_deducted,
        'is_voided' => $i->is_voided,
    ]);
@endphp

<div class="flex h-screen flex-col lg:flex-row" x-data='{ 
    showCheckout: false,
    paymentMethod: "cash",
    amountReceived: 0,
    gcashRef: "",
    location: "",
    diningOption: "",
    isCheckingOut: false,
    isUnlocked: false,
    processingItems: [], 

    cart: {
        items: @json($cartData),
        total: {{ (float)$activeOrderTotal }},
        count: {{ (int)$activeOrderItemCount }}
    },
    get total() { return this.cart.total },
    get change() { return Math.max(0, this.amountReceived - this.total) },

    init() {
        this.$nextTick(() => {
            if (typeof lucide !== "undefined") lucide.createIcons();
        });
    },
    
    async postData(url, formData, key = null, isItem = false) {
        if (key) {
            if (isItem) this.processingItems.push(parseInt(key));
            else this[key] = true;
        }
        try {
            const response = await fetch(url, {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "Accept": "application/json"
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.cart = data.cart;
            } else {
                this.$dispatch("notify", { message: data.message || "Something went wrong", type: "error" });
            }
        } catch (error) {
            console.error("Error:", error);
            this.$dispatch("notify", { message: "Connection error. Please try again.", type: "error" });
        } finally {
            if (key) {
                if (isItem) this.processingItems = this.processingItems.filter(id => id !== parseInt(key));
                else this[key] = false;
            }
            this.$nextTick(() => {
                if (typeof lucide !== "undefined") lucide.createIcons();
            });
        }
    },

    async submitForm(e) {
        const form = e.target;
        const formData = new FormData(form);
        const menuItemId = formData.get("menu_item_id");
        await this.postData(form.action, formData, menuItemId, true);
    },

    async updateItemQuantity(item, newQty) {
        newQty = parseInt(newQty);
        if (isNaN(newQty) || newQty < 1) return;
        
        const formData = new FormData();
        formData.append("_token", "{{ csrf_token() }}");
        formData.append("quantity", newQty);
        formData.append("admin_id", "1");
        await this.postData(`{{ url('orders/update-quantity') }}/${item.id}`, formData, item.menu_item_id, true);
    },

    unlockManagerMode() {
        this.openPinModal((adminId) => {
            this.isUnlocked = true;
            this.$nextTick(() => {
                if (typeof lucide !== "undefined") lucide.createIcons();
            });
        });
    },

    isMaxed(itemId, stockQty) {
        if (stockQty === null) return false;
        const inCart = this.cart.items.find(i => i.menu_item_id === itemId);
        return inCart && inCart.quantity >= stockQty;
    },

    isProcessing(itemId) {
        return this.processingItems.includes(parseInt(itemId));
    }
}' @notify.window='
    const id = Date.now();
    notifications.push({ id, ...$event.detail });
    setTimeout(() => {
        const index = notifications.findIndex(n => n.id === id);
        if (index > -1) notifications.splice(index, 1);
    }, 5000);
'>
    <!-- Categories Sidebar -->
    <div class="w-full lg:w-48 bg-gray-50 border-r border-gray-200 p-4 shrink-0 overflow-y-auto">
        <h2 class="text-xs font-semibold text-gray-500 uppercase mb-4 px-2 hidden lg:block">Categories</h2>
        <div class="flex lg:flex-col gap-2 overflow-x-auto lg:overflow-x-visible pb-2 lg:pb-0">
            <a href="{{ route('orders.index', ['category' => 'all']) }}" 
               class="flex-shrink-0 lg:w-full p-4 rounded-lg text-left transition-all active:scale-95 font-medium border {{ $selectedCategory === 'all' ? 'bg-[#6366f1] text-white' : 'bg-white border-gray-200 hover:bg-gray-100' }}">
                <div class="text-xl mb-1">📋</div>
                <div class="text-xs">All Items</div>
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('orders.index', ['category' => $cat->slug]) }}" 
                   class="flex-shrink-0 lg:w-full p-4 rounded-lg text-left transition-all active:scale-95 font-medium border {{ $selectedCategory === $cat->slug ? 'bg-[#6366f1] text-white' : 'bg-white border-gray-200 hover:bg-gray-100' }}">
                    <div class="text-xl mb-1">{{ $cat->icon }}</div>
                    <div class="text-xs">{{ $cat->name }}</div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6 md:p-8 overflow-y-auto">
        <div class="max-w-6xl mx-auto">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Short Orders</h1>
                <p class="text-gray-600">Quick POS for walk-in customers</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($items as $item)
                    @php
                        $outOfStock = $item->isOutOfStock();
                        $status = $item->stockStatus();
                    @endphp

                    <form action="{{ route('orders.add-item') }}" method="POST" @submit.prevent="submitForm">
                        @csrf
                        <input type="hidden" name="menu_item_id" value="{{ $item->id }}">
                        <button type="submit"
                            @class([
                                'relative w-full border-2 rounded-xl p-6 flex flex-col gap-4 text-left transition-all duration-200',
                                'bg-white border-gray-200 hover:border-[#6366f1] hover:shadow-md cursor-pointer' => !$outOfStock,
                                'bg-gray-100 border-gray-200 cursor-not-allowed opacity-60' => $outOfStock,
                            ])
                            :disabled="{{ $outOfStock ? 'true' : 'false' }} || isMaxed({{ $item->id }}, {{ $item->stock_quantity ?? 'null' }}) || isProcessing({{ $item->id }})">

                            <div class="absolute inset-0 flex items-center justify-center rounded-xl bg-white/60 z-20" 
                                 x-show="isProcessing({{ $item->id }})" x-cloak>
                                <i class="animate-spin text-indigo-600 w-6 h-6" data-lucide="loader-2"></i>
                            </div>

                            <h3 class="font-semibold text-gray-900 line-clamp-2 flex-1">{{ $item->name }}</h3>
                            
                            <div class="flex items-center justify-between">
                                <div class="text-2xl font-bold text-gray-900">₱{{ number_format($item->price) }}</div>
                                @if($status !== 'unlimited')
                                    <div class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $status === 'out_of_stock' ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                                        {{ $item->stock_quantity }}
                                    </div>
                                @endif
                            </div>
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="w-full lg:w-96 bg-gray-50 border-l border-gray-200 flex flex-col shadow-xl">
        <div class="p-4 border-b border-gray-200 bg-white">
            <div class="flex items-center gap-3">
                <i data-lucide="shopping-cart" class="w-5 h-5 text-[#6366f1]"></i>
                <h3 class="text-lg font-bold">Order Summary</h3>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-3">
            <template x-for="item in cart.items" :key="item.id">
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <div class="flex justify-between items-start gap-4 mb-2">
                        <div class="flex-1">
                            <div class="font-bold text-gray-900 text-sm" x-text="item.name"></div>
                            <div class="text-xs text-gray-500" x-text="'₱' + Number(item.unit_price).toLocaleString()"></div>
                        </div>

                        <template x-if="isUnlocked">
                            <button @click="
                                const formData = new FormData();
                                formData.append('_token', '{{ csrf_token() }}');
                                formData.append('_method', 'DELETE');
                                formData.append('admin_id', '1');
                                postData(`{{ url('orders/remove-item') }}/${item.id}`, formData, item.menu_item_id, true);
                            " class="text-red-500 hover:bg-red-50 p-2 rounded-lg transition-all">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </template>
                    </div>

                    <div class="flex items-center justify-between border-t border-gray-100 pt-2 mt-2">
                        <div class="text-[#6366f1] font-bold" x-text="'₱' + Number(item.total).toLocaleString()"></div>
                        
                        <div>
                            <template x-if="!isUnlocked">
                                <div class="text-sm font-bold text-gray-600">Qty: <span x-text="item.quantity"></span></div>
                            </template>

                            <template x-if="isUnlocked">
                                <input type="number" min="1" 
                                       :value="item.quantity" 
                                       @change="updateItemQuantity(item, $event.target.value)"
                                       class="w-16 text-center font-bold text-sm border-2 border-gray-200 rounded-lg py-1 focus:border-[#6366f1] focus:outline-none">
                            </template>
                        </div>
                    </div>
                </div>
            </template>
            
            <template x-if="cart.items.length === 0">
                <div class="py-20 text-center text-gray-400 font-medium">Cart is Empty</div>
            </template>
        </div>

        <div class="p-6 border-t border-gray-200 bg-white space-y-4">
            <div class="flex justify-between items-center text-2xl font-bold">
                <span>Total</span>
                <span class="text-[#ec4899]" x-text="'₱' + Number(total).toLocaleString()"></span>
            </div>

            <button @click="showCheckout = true" :disabled="total <= 0" 
                    class="w-full py-4 bg-[#10b981] text-white rounded-xl hover:bg-[#059669] active:scale-95 transition-all shadow-md font-bold text-lg disabled:opacity-50">
                Checkout
            </button>
            
            <button @click="unlockManagerMode()" 
                    x-show="!isUnlocked"
                    :disabled="total <= 0"
                    class="w-full py-3 bg-amber-50 text-amber-600 rounded-xl border border-amber-200 hover:bg-amber-100 transition-all flex items-center justify-center gap-2 font-bold uppercase text-xs disabled:opacity-50">
                <i data-lucide="lock" class="w-4 h-4"></i>
                <span>Void Mode (Admin PIN)</span>
            </button>

            <div x-show="isUnlocked" class="w-full py-3 bg-emerald-50 text-emerald-600 rounded-xl border border-emerald-200 flex items-center justify-center gap-2 font-bold uppercase text-xs">
                <i data-lucide="unlock" class="w-4 h-4"></i>
                <span>Manager Mode Unlocked</span>
            </div>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" x-show="showCheckout" x-transition x-cloak>
        <div class="bg-white rounded-2xl p-6 md:p-8 max-w-md w-full relative shadow-2xl" @click.away="showCheckout = false">
            <button @click="showCheckout = false" class="absolute top-4 right-4 p-2 hover:bg-gray-100 rounded-lg">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>

            <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center uppercase tracking-tight">Payment Checkout</h2>

            <form action="{{ route('orders.checkout') }}" method="POST" @submit="isCheckingOut = true">
                @csrf
                <div class="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <div class="text-xs font-bold text-gray-400 uppercase mb-1">Total Amount</div>
                    <div class="text-3xl font-bold text-[#6366f1]" x-text="'₱' + Number(total).toLocaleString()"></div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2 px-1">Payment Method</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="cash" x-model="paymentMethod" class="sr-only peer">
                                <div class="py-3 rounded-lg font-bold text-center border-2 border-gray-100 text-gray-400 peer-checked:bg-[#6366f1] peer-checked:text-white peer-checked:border-[#6366f1] uppercase text-xs">Cash</div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="gcash" x-model="paymentMethod" class="sr-only peer">
                                <div class="py-3 rounded-lg font-bold text-center border-2 border-gray-100 text-gray-400 peer-checked:bg-[#6366f1] peer-checked:text-white peer-checked:border-[#6366f1] uppercase text-xs">GCash</div>
                            </label>
                        </div>
                    </div>

                    <div x-show="paymentMethod === 'cash'" x-transition>
                        <div class="space-y-4">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-500 uppercase px-1">Amount Received</label>
                                <input type="number" name="amount_received" x-model.number="amountReceived" required class="w-full px-4 py-3 bg-gray-50 border-2 border-transparent rounded-lg focus:border-[#6366f1] focus:bg-white focus:outline-none font-bold text-2xl">
                            </div>
                            <div class="p-4 bg-emerald-50 rounded-lg flex justify-between items-center font-bold">
                                <span class="text-xs text-emerald-600 uppercase">Change</span>
                                <span class="text-2xl text-emerald-700">₱<span x-text="change.toLocaleString()"></span></span>
                            </div>
                        </div>
                    </div>

                    <div x-show="paymentMethod === 'gcash'" x-transition x-cloak>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-500 uppercase px-1">GCash Reference</label>
                            <input type="text" name="reference_number" x-model="gcashRef" maxlength="13" :required="paymentMethod === 'gcash'" placeholder="13-digit Ref #" class="w-full px-4 py-3 bg-gray-50 border-2 border-transparent rounded-lg focus:border-[#6366f1] focus:bg-white focus:outline-none font-bold">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2 px-1">Location</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" name="location" value="bar" x-model="location" class="sr-only peer" required>
                                    <div class="py-2 rounded-lg font-bold text-center border-2 border-gray-100 text-gray-400 peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500 uppercase text-xs">Bar</div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="location" value="cafe" x-model="location" class="sr-only peer" required>
                                    <div class="py-2 rounded-lg font-bold text-center border-2 border-gray-100 text-gray-400 peer-checked:bg-blue-500 peer-checked:text-white peer-checked:border-blue-500 uppercase text-xs">Cafe</div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2 px-1">Dining Option</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" name="dining_option" value="dine-in" x-model="diningOption" class="sr-only peer" required>
                                    <div class="py-2 rounded-lg font-bold text-center border-2 border-gray-100 text-gray-400 peer-checked:bg-emerald-500 peer-checked:text-white peer-checked:border-emerald-500 uppercase text-xs">Dine-in</div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="dining_option" value="takeout" x-model="diningOption" class="sr-only peer" required>
                                    <div class="py-2 rounded-lg font-bold text-center border-2 border-gray-100 text-gray-400 peer-checked:bg-amber-500 peer-checked:text-white peer-checked:border-amber-500 uppercase text-xs">Takeout</div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 mt-8">
                    <button type="button" @click="showCheckout = false" class="flex-1 py-4 bg-gray-100 text-gray-600 rounded-lg font-bold text-sm uppercase">Back</button>
                    <button type="submit" :disabled="isCheckingOut || !location || !diningOption || (paymentMethod === 'cash' && (amountReceived < total || total <= 0)) || (paymentMethod === 'gcash' && gcashRef.length !== 13)" 
                            class="flex-1 py-4 bg-[#6366f1] text-white rounded-lg font-bold text-sm uppercase shadow-lg disabled:opacity-50">
                        <span x-show="!isCheckingOut">Complete Order</span>
                        <span x-show="isCheckingOut">Processing...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
