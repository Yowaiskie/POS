<!-- Room Orders Modal -->
<div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" x-show="showOrdersModal" x-transition x-cloak>
    <div class="bg-white border-2 border-gray-200 rounded-2xl max-w-6xl w-full h-[90vh] flex flex-col relative shadow-2xl" @click.away="showOrdersModal = false">
        <!-- Modal Header -->
        <div class="p-6 border-b border-gray-200 flex items-center justify-between bg-gray-50">
            <div class="flex items-center gap-3">
                <h2 class="text-2xl md:text-3xl font-bold" x-text="activeRoom ? activeRoom.name + ' - Room Orders' : 'Room Orders'"></h2>
            </div>
            <button @click="showOrdersModal = false" class="p-2 hover:bg-gray-200 rounded-lg transition-colors active:scale-95">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <div class="flex flex-1 overflow-hidden">
            <!-- Left: Categories -->
            <div class="w-48 border-r border-gray-200 p-4 space-y-2 bg-gray-50 hidden md:block overflow-y-auto">
                @foreach($categories as $cat)
                <button @click="selectedCategory = '{{ $cat->slug }}'"
                        class="w-full p-4 rounded-lg text-left transition-all active:scale-95 font-medium border"
                        :class="selectedCategory === '{{ $cat->slug }}' ? 'bg-[#6366f1] text-white shadow-md' : 'bg-white hover:bg-gray-100 border-gray-200'">
                    <div class="text-sm">{{ $cat->name }}</div>
                </button>
                @endforeach
            </div>

            <div class="flex-1 p-6 overflow-y-auto">
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($items as $item)
                        @php
                            $status = $item->stockStatus();
                            $outOfStock = $item->isOutOfStock();
                        @endphp
                        <form action="{{ route('orders.add-item') }}" method="POST" x-show="selectedCategory === '{{ $item->category->slug }}'" x-transition>
                            @csrf
                            <input type="hidden" name="menu_item_id" value="{{ $item->id }}">
                            <input type="hidden" name="room_session_id" :value="activeSession ? activeSession.id : ''">
                            <button type="submit" 
                                    @class([
                                        'w-full p-6 rounded-xl border-2 transition-all text-left h-full flex flex-col',
                                        'bg-white border-gray-200 hover:border-[#6366f1] hover:shadow-md cursor-pointer active:scale-95' => !$outOfStock,
                                        'bg-slate-50 border-gray-100 cursor-not-allowed opacity-60' => $outOfStock,
                                    ])
                                    {{ $outOfStock ? 'disabled' : '' }}>
                                <h4 class="mb-2 font-semibold text-slate-900 line-clamp-2 min-h-[3rem] flex-1">{{ $item->name }}</h4>
                                <div class="flex items-end justify-between gap-2">
                                    <div class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">₱{{ number_format($item->price) }}</div>
                                    
                                    <div class="mb-1">
                                        @if($status === 'unlimited')
                                            {{-- Nothing --}}
                                        @elseif($status === 'out_of_stock')
                                            <span class="px-2 py-0.5 bg-red-100 text-red-700 text-[10px] font-bold rounded-full">OUT</span>
                                        @elseif($status === 'low')
                                            <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-full">{{ $item->stock_quantity }} left</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full">{{ $item->stock_quantity }} left</span>
                                        @endif
                                    </div>
                                </div>
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>

            <!-- Right: Order Summary -->
            <div class="w-80 border-l border-gray-200 bg-gray-50 flex flex-col">
                <div class="p-4 border-b border-gray-200 bg-white">
                    <div class="flex items-center gap-3">
                        <i data-lucide="shopping-cart" class="w-5 h-5 text-[#6366f1]"></i>
                        <span class="text-lg font-semibold">Order Summary</span>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-4 space-y-2">
                    <template x-if="activeSession && activeSession.orders">
                        <template x-for="order in activeSession.orders" :key="order.id">
                            <div class="space-y-2">
                                <template x-for="item in order.items" :key="item.id">
                                    <div class="bg-white rounded-lg border border-gray-200 p-3 shadow-sm">
                                         <div class="flex justify-between items-start gap-2 mb-2">
                                             <div class="flex-1">
                                                 <div class="font-medium text-sm text-slate-900" x-text="item.name"></div>
                                                 <div class="text-xs text-gray-500" x-text="'₱' + Number(item.unit_price).toLocaleString() + ' x ' + item.quantity"></div>
                                                 
                                                 <template x-if="item.menu_item && item.menu_item.stock_quantity !== null">
                                                     <div class="text-[10px] font-bold mt-1" 
                                                          :class="item.quantity >= item.menu_item.stock_quantity ? 'text-red-500' : 'text-slate-400'">
                                                        <span x-text="'Stock: ' + item.menu_item.stock_quantity"></span>
                                                    </div>
                                                </template>
                                            </div>
                                            <form :action="`{{ url('orders/remove-item') }}/${item.id}`" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 hover:bg-red-100 rounded transition-colors text-red-600">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <div class="font-semibold text-[#6366f1]" x-text="'₱' + (item.unit_price * item.quantity).toLocaleString()"></div>
                                            <div class="flex items-center gap-2">
                                                <form :action="`{{ url('orders/update-quantity') }}/${item.id}`" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="delta" value="-1">
                                                    <button type="submit" class="w-6 h-6 flex items-center justify-center bg-slate-100 hover:bg-slate-200 rounded text-slate-600">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                                    </button>
                                                </form>
                                                <span class="text-sm font-bold w-4 text-center" x-text="item.quantity"></span>
                                                <form :action="`{{ url('orders/update-quantity') }}/${item.id}`" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="delta" value="1">
                                                    <button type="submit" 
                                                            class="w-6 h-6 flex items-center justify-center bg-[#6366f1] text-white hover:bg-indigo-700 rounded shadow-sm disabled:opacity-30 disabled:cursor-not-allowed"
                                                            :disabled="item.menu_item && item.menu_item.stock_quantity !== null && item.quantity >= item.menu_item.stock_quantity">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </template>
                    <template x-if="!activeSession || !activeSession.orders || activeSession.orders.length === 0 || activeSession.orders.every(o => o.items.length === 0)">
                        <div class="h-full flex flex-col items-center justify-center text-center p-8 opacity-40">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-12 h-12 mb-4 text-slate-300"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                            <p class="text-sm font-semibold text-slate-400">Cart is empty</p>
                        </div>
                    </template>
                </div>

                <div class="p-4 border-t border-gray-200 bg-white space-y-3">
                    <div class="flex items-center justify-between text-xl font-bold">
                        <span>Total</span>
                        <span class="text-[#ec4899]" x-text="'₱' + totalAmount.toLocaleString()"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <form :action="`{{ url('rooms/sessions') }}/${activeSession ? activeSession.id : ''}/extend`" method="POST">
                            @csrf
                            <input type="hidden" name="duration" value="30">
                            <button type="submit" class="w-full px-4 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 active:scale-95 transition-all text-xs font-bold shadow-sm">
                                +30 Min
                            </button>
                        </form>
                        <form :action="`{{ url('rooms/sessions') }}/${activeSession ? activeSession.id : ''}/extend`" method="POST">
                            @csrf
                            <input type="hidden" name="duration" value="60">
                            <button type="submit" class="w-full px-4 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 active:scale-95 transition-all text-xs font-bold shadow-sm">
                                +1 Hour
                            </button>
                        </form>
                    </div>

                    <button @click="showOrdersModal = false" 
                            class="w-full px-6 py-3 bg-[#10b981] text-white rounded-lg hover:bg-[#059669] active:scale-95 transition-all shadow-md font-medium">
                        Submit Order
                    </button>
                    
                    <button @click="openBillOutModal(activeRoom, activeSession); showOrdersModal = false"
                            class="w-full px-6 py-3 bg-[#ec4899] text-white rounded-lg hover:bg-[#db2777] active:scale-95 transition-all shadow-md font-medium">
                        Bill Out
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
