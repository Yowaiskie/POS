<!-- Bill Out Modal -->
<div class="fixed inset-0 bg-black/60 z-[60] flex items-center justify-center p-4" x-show="showBillOutModal" x-transition x-cloak @click="showBillOutModal = false">
    <div class="bg-white border-2 border-gray-200 rounded-2xl p-6 md:p-8 max-w-2xl w-full relative shadow-2xl max-h-[90vh] overflow-y-auto" @click.stop>
        <button @click="showBillOutModal = false" class="absolute top-4 right-4 p-2 hover:bg-gray-100 rounded-lg transition-colors active:scale-95 z-10">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>

        <h2 class="text-2xl md:text-3xl mb-6 font-bold text-center text-slate-900">Bill Out</h2>

        <div class="bg-slate-50 border-2 border-slate-200 rounded-2xl p-6 mb-8 text-center shadow-inner">
            <div class="text-lg font-bold text-slate-600 mb-2 uppercase tracking-wide" x-text="activeRoom ? activeRoom.name : ''"></div>
            <div class="text-6xl font-black text-indigo-600 mb-2 drop-shadow-sm" x-text="'₱' + Math.floor(totalAmount).toLocaleString()"></div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Amount Due</div>
        </div>

        <!-- Order Breakdown -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <i data-lucide="receipt" class="w-5 h-5 text-indigo-600"></i>
                    <h3 class="text-lg font-bold text-slate-800">Order Summary</h3>
                </div>
                <template x-if="activeSession && activeSession.orders && activeSession.orders.length > 0">
                    <span class="text-[10px] font-mono bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full border border-indigo-200">
                        REF: <span x-text="activeSession.orders[0].transaction_id"></span>
                    </span>
                </template>
            </div>
            
            <div class="bg-white border-2 border-slate-100 rounded-2xl overflow-hidden shadow-sm">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="text-left px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Description</th>
                            <th class="text-center px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Qty</th>
                            <th class="text-right px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Price</th>
                            <th class="text-right px-4 py-3 text-[10px] font-bold text-indigo-600 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <!-- Included Room Time (Promo) -->
                        <template x-if="activeSession && activeSession.promo_duration_hours > 0">
                            <tr class="bg-emerald-50/30">
                                <td class="px-4 py-4">
                                    <div class="text-sm font-bold text-slate-800">Included Room Time</div>
                                    <div class="text-[10px] text-emerald-600 font-medium italic" x-text="activeSession.promo_duration_hours + ' hr(s) from ' + activeSession.orders[0].promo_name"></div>
                                </td>
                                <td class="px-4 py-4 text-sm text-center text-slate-500">1</td>
                                <td class="px-4 py-4 text-sm text-right text-slate-500">₱0</td>
                                <td class="px-4 py-4 text-sm text-right font-bold text-emerald-600">₱0</td>
                            </tr>
                        </template>

                        <!-- Room Charges (Extra) -->
                        <template x-if="roomBilling.extension > 0">
                            <tr class="bg-indigo-50/30">
                                <td class="px-4 py-4">
                                    <div class="text-sm font-bold text-slate-800">Room Extension</div>
                                    <div class="text-[10px] text-indigo-600 font-medium italic" x-text="roomBilling.extensionDesc"></div>
                                </td>
                                <td class="px-4 py-4 text-sm text-center text-slate-500">1</td>
                                <td class="px-4 py-4 text-sm text-right text-slate-500" x-text="'₱' + roomBilling.extension.toLocaleString()"></td>
                                <td class="px-4 py-4 text-sm text-right font-bold text-indigo-600" x-text="'₱' + roomBilling.extension.toLocaleString()"></td>
                            </tr>
                        </template>

                        <template x-if="roomBilling.overtime > 0">
                            <tr class="bg-rose-50/30">
                                <td class="px-4 py-4">
                                    <div class="text-sm font-bold text-rose-800">Room Overtime</div>
                                    <div class="text-[10px] text-rose-600 font-medium italic" x-text="roomBilling.overtimeDesc"></div>
                                </td>
                                <td class="px-4 py-4 text-sm text-center text-slate-500">1</td>
                                <td class="px-4 py-4 text-sm text-right text-slate-500" x-text="'₱' + roomBilling.overtime.toLocaleString()"></td>
                                <td class="px-4 py-4 text-sm text-right font-bold text-rose-600" x-text="'₱' + roomBilling.overtime.toLocaleString()"></td>
                            </tr>
                        </template>

                        <!-- Food Orders -->
                        <template x-if="activeSession">
                            <template x-for="order in activeSession.orders" :key="order.id">
                                <template x-for="item in order.items" :key="item.id">
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-medium text-slate-700" x-text="item.name"></div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-center text-slate-500" x-text="item.quantity"></td>
                                        <td class="px-4 py-3 text-sm text-right text-slate-500" x-text="'₱' + parseFloat(item.unit_price).toLocaleString()"></td>
                                        <td class="px-4 py-3 text-sm text-right font-semibold text-slate-700" x-text="'₱' + (item.unit_price * item.quantity).toLocaleString()"></td>
                                    </tr>
                                </template>
                            </template>
                        </template>
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-100">
                        <tr>
                            <td colspan="3" class="px-4 py-4 text-right text-sm font-bold text-slate-500 uppercase tracking-widest">Grand Total</td>
                            <td class="px-4 py-4 text-right font-black text-xl text-indigo-600" x-text="'₱' + Math.floor(totalAmount).toLocaleString()"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Payment Logic -->
        <div x-show="!paymentMethod">
            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-700 mb-4 uppercase tracking-wide">Select Payment Method</label>
                <div class="grid grid-cols-2 gap-4">
                    <button @click="paymentMethod = 'cash'" 
                            class="group p-6 bg-white border-2 border-slate-100 rounded-2xl hover:border-indigo-600 hover:bg-indigo-50 transition-all flex flex-col items-center gap-3">
                        <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                            <i data-lucide="banknote" class="w-6 h-6"></i>
                        </div>
                        <span class="font-bold text-slate-700 group-hover:text-indigo-700">Cash</span>
                    </button>
                    <button @click="paymentMethod = 'gcash'" 
                            class="group p-6 bg-white border-2 border-slate-100 rounded-2xl hover:border-indigo-600 hover:bg-indigo-50 transition-all flex flex-col items-center gap-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all">
                            <i data-lucide="smartphone" class="w-6 h-6"></i>
                        </div>
                        <span class="font-bold text-slate-700 group-hover:text-indigo-700">G-Cash</span>
                    </button>
                </div>
            </div>
            <button @click="showBillOutModal = false" class="w-full py-4 text-slate-400 font-bold hover:text-slate-600 transition-all uppercase tracking-widest">Close</button>
        </div>

        <!-- Cash Payment Form -->
        <div x-show="paymentMethod === 'cash'" x-transition>
            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-700 mb-2">Amount Received</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-slate-400">₱</span>
                    <input type="number" x-model="amountReceived" placeholder="0.00" 
                           class="w-full pl-10 pr-4 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-indigo-500 focus:bg-white outline-none text-2xl font-bold">
                </div>
            </div>

            <div x-show="receivedAmount > 0" class="mb-8 p-6 bg-indigo-600 rounded-2xl text-white shadow-lg shadow-indigo-200" x-transition>
                <div class="flex justify-between items-center mb-4 border-b border-indigo-400/30 pb-4">
                    <span class="text-sm font-medium opacity-80 uppercase tracking-wider">Change</span>
                    <span class="text-3xl font-black" x-text="'₱' + Math.max(0, change).toLocaleString()"></span>
                </div>
                <div class="flex justify-between items-center text-xs opacity-60">
                    <span>Target: ₱<span x-text="Math.floor(totalAmount).toLocaleString()"></span></span>
                    <span>Input: ₱<span x-text="receivedAmount.toLocaleString()"></span></span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <button @click="paymentMethod = null" class="py-4 font-bold text-slate-400 hover:text-slate-600 transition-all uppercase tracking-widest">Back</button>
                <form :action="`{{ url('rooms/sessions') }}/${activeSession ? activeSession.id : ''}/bill-out`" method="POST">
                    @csrf
                    <input type="hidden" name="payment_method" value="cash">
                    <input type="hidden" name="amount_received" :value="receivedAmount">
                    <button type="submit" :disabled="receivedAmount < Math.floor(totalAmount)" 
                            class="w-full py-4 bg-indigo-600 text-white rounded-2xl hover:bg-indigo-700 active:scale-95 transition-all shadow-xl shadow-indigo-100 font-bold disabled:opacity-50 disabled:grayscale uppercase tracking-widest">
                        Confirm Payment
                    </button>
                </form>
            </div>
        </div>

        <!-- GCash Payment Form -->
        <div x-show="paymentMethod === 'gcash'" x-transition>
            <div class="mb-8">
                <label class="block text-sm font-bold text-slate-700 mb-2">GCash Reference Number</label>
                <input type="text" x-model="transactionNumber" 
                       maxlength="13"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                       placeholder="13-digit Reference #" 
                       class="w-full px-4 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:border-indigo-500 focus:bg-white outline-none text-xl font-mono">
                <p class="mt-2 text-[10px] text-slate-400 font-bold uppercase tracking-widest italic text-center">Verify the reference number from the customer's GCash receipt</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <button @click="paymentMethod = null" class="py-4 font-bold text-slate-400 hover:text-slate-600 transition-all uppercase tracking-widest">Back</button>
                <form :action="`{{ url('rooms/sessions') }}/${activeSession ? activeSession.id : ''}/bill-out`" method="POST">
                    @csrf
                    <input type="hidden" name="payment_method" value="gcash">
                    <input type="hidden" name="reference_number" :value="transactionNumber">
                    <button type="submit" :disabled="transactionNumber.length !== 13" 
                            class="w-full py-4 bg-blue-600 text-white rounded-2xl hover:bg-blue-700 active:scale-95 transition-all shadow-xl shadow-blue-100 font-bold disabled:opacity-50 disabled:grayscale uppercase tracking-widest">
                        Verify & Complete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
