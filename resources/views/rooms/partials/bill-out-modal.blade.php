<!-- Bill Out Modal -->
<div class="fixed inset-0 bg-black/60 z-[60] flex items-center justify-center p-4" x-show="showBillOutModal" x-transition x-cloak @click="showBillOutModal = false">
    <div class="bg-white border-2 border-gray-200 rounded-2xl p-6 md:p-8 max-w-2xl w-full relative shadow-2xl max-h-[90vh] overflow-y-auto" @click.stop>
        <button @click="showBillOutModal = false" class="absolute top-4 right-4 p-2 hover:bg-gray-100 rounded-lg transition-colors active:scale-95 z-10">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>

        <h2 class="text-2xl md:text-3xl mb-6 font-bold text-center">Bill Out</h2>

        <div class="bg-gray-50 border-2 border-gray-200 rounded-xl p-6 mb-6 text-center">
            <div class="text-lg font-semibold text-gray-600 mb-1" x-text="activeRoom ? activeRoom.name : ''"></div>
            <template x-if="activeSession && activeSession.orders && activeSession.orders.length > 0">
                <div class="mb-3">
                    <span class="text-[10px] font-mono bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded">
                        POS ID: <span x-text="activeSession.orders[0].transaction_id"></span>
                    </span>
                </div>
            </template>
            <div class="text-5xl font-bold text-[#6366f1] mb-2" x-text="'₱' + totalAmount.toLocaleString()"></div>
            <div class="text-sm text-gray-500 font-medium">Total Amount Due</div>
        </div>

        <!-- Order Breakdown -->
        <template x-if="activeSession && activeSession.orders">
            <div class="mb-6">
                <div class="flex items-center gap-2 mb-3">
                    <i data-lucide="shopping-bag" class="w-5 h-5 text-[#6366f1]"></i>
                    <h3 class="text-lg font-bold">Order Breakdown</h3>
                </div>
                <div class="bg-white border-2 border-gray-200 rounded-xl overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <th class="text-left px-4 py-3 text-sm font-semibold text-gray-700">Item</th>
                                <th class="text-center px-4 py-3 text-sm font-semibold text-gray-700">Qty</th>
                                <th class="text-right px-4 py-3 text-sm font-semibold text-gray-700">Price</th>
                                <th class="text-right px-4 py-3 text-sm font-semibold text-gray-700 text-[#6366f1]">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="order in activeSession.orders" :key="order.id">
                                <template x-for="item in order.items" :key="item.id">
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900" x-text="item.name"></td>
                                        <td class="px-4 py-3 text-sm text-center text-gray-600" x-text="item.quantity"></td>
                                        <td class="px-4 py-3 text-sm text-right text-gray-600" x-text="'₱' + Number(item.unit_price).toLocaleString()"></td>
                                        <td class="px-4 py-3 text-sm text-right font-semibold text-[#6366f1]" x-text="'₱' + (item.unit_price * item.quantity).toLocaleString()"></td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                        <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right font-bold text-gray-900 text-base">Grand Total:</td>
                                <td class="px-4 py-3 text-right font-bold text-lg text-[#ec4899]" x-text="'₱' + totalAmount.toLocaleString()"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </template>

        <!-- Payment Logic -->
        <div x-show="!paymentMethod">
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-3">Select Payment Method</label>
                <div class="grid grid-cols-2 gap-3">
                    <button @click="paymentMethod = 'cash'" 
                            class="px-6 py-4 bg-white border-2 border-gray-200 text-gray-700 rounded-lg hover:border-[#6366f1] hover:bg-gray-50 active:scale-95 transition-all font-medium flex flex-col items-center gap-2">
                        <i data-lucide="banknote" class="w-6 h-6"></i>
                        Cash
                    </button>
                    <button @click="paymentMethod = 'gcash'" 
                            class="px-6 py-4 bg-white border-2 border-gray-200 text-gray-700 rounded-lg hover:border-[#6366f1] hover:bg-gray-50 active:scale-95 transition-all font-medium flex flex-col items-center gap-2">
                        <i data-lucide="smartphone" class="w-6 h-6"></i>
                        G-Cash
                    </button>
                </div>
            </div>
            <button @click="showBillOutModal = false" class="w-full px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 active:scale-95 transition-all font-medium">Cancel</button>
        </div>

        <div x-show="paymentMethod === 'gcash'">
            <div class="mb-6">
                <label class="block text-sm font-semibold text-slate-700 mb-2">GCash Reference Number</label>
                <input type="text" x-model="transactionNumber" 
                       maxlength="13"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                       placeholder="Enter 13-digit Ref #" 
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-indigo-500 focus:outline-none text-lg">
                <p class="mt-1 text-xs text-slate-500">Provided by the customer from their GCash app</p>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <button @click="paymentMethod = null" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 active:scale-95 transition-all font-medium">Back</button>
                <form :action="`{{ url('rooms/sessions') }}/${activeSession ? activeSession.id : ''}/bill-out`" method="POST">
                    @csrf
                    <input type="hidden" name="payment_method" value="gcash">
                    <input type="hidden" name="reference_number" :value="transactionNumber">
                    <button type="submit" :disabled="transactionNumber.length !== 13" 
                            class="w-full px-6 py-3 bg-[#ec4899] text-white rounded-lg hover:bg-[#db2777] active:scale-95 transition-all shadow-md font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                        Bill Out
                    </button>
                </form>
            </div>
        </div>

        <div x-show="paymentMethod === 'cash'">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Amount Received</label>
                <input type="number" x-model="amountReceived" placeholder="Enter amount received" 
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-indigo-500 focus:outline-none text-lg">
            </div>

            <div x-show="receivedAmount > 0" class="mb-6 p-4 bg-indigo-50 rounded-lg border border-indigo-200">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-slate-600">Total Amount</span>
                    <span class="font-semibold text-slate-900" x-text="'₱' + totalAmount.toLocaleString()"></span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-slate-600">Amount Received</span>
                    <span class="font-semibold text-slate-900" x-text="'₱' + receivedAmount.toLocaleString()"></span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-indigo-300">
                    <span class="text-sm font-semibold text-slate-700">Change</span>
                    <span class="text-xl font-bold text-indigo-600" x-text="'₱' + change.toLocaleString()"></span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <button @click="paymentMethod = null" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 active:scale-95 transition-all font-medium">Back</button>
                <form :action="`{{ url('rooms/sessions') }}/${activeSession ? activeSession.id : ''}/bill-out`" method="POST">
                    @csrf
                    <input type="hidden" name="payment_method" value="cash">
                    <input type="hidden" name="amount_received" :value="receivedAmount">
                    <button type="submit" :disabled="receivedAmount < totalAmount" 
                            class="w-full px-6 py-3 bg-[#ec4899] text-white rounded-lg hover:bg-[#db2777] active:scale-95 transition-all shadow-md font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                        Bill Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
