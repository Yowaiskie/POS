<!-- Room Detail Modal -->
<div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" x-show="showDetailModal" x-transition x-cloak @click="showDetailModal = false">
    <div class="bg-white border-2 border-gray-200 rounded-2xl p-6 md:p-8 max-w-3xl w-full relative shadow-2xl max-h-[90vh] overflow-y-auto" @click.stop>
        <button @click="showDetailModal = false" class="absolute top-4 right-4 p-2 hover:bg-gray-100 rounded-lg transition-colors active:scale-95 z-10">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>

        <h2 class="text-2xl md:text-3xl mb-6 md:mb-8 font-bold" x-text="activeRoom ? activeRoom.name : ''"></h2>

        <div class="flex justify-center mb-6 md:mb-8 bg-gray-50 rounded-xl py-6" x-data="{ timer: '00:00:00', isOvertime: false, status: '' }" 
             x-init="$watch('activeSession', (session) => {
                if(!session) return;
                const update = () => {
                    const end = new Date(session.ends_at);
                    const now = new Date();
                    const diff = end - now;
                    isOvertime = diff < 0;
                    const absDiff = Math.abs(diff) / 1000;
                    const h = Math.floor(absDiff / 3600);
                    const m = Math.floor((absDiff % 3600) / 60);
                    const s = Math.floor(absDiff % 60);
                    timer = [h, m, s].map(v => v.toString().padStart(2, '0')).join(':');
                };
                update();
                setInterval(update, 1000);
             })">
            <div class="flex flex-col items-center">
                <div class="font-mono text-5xl md:text-6xl tracking-wider font-bold"
                     :class="isOvertime ? 'text-[--status-danger]' : 'text-[--status-active]'"
                     :style="{
                        textShadow: isOvertime ? '0 0 20px var(--status-danger)' : '0 0 10px var(--status-active)'
                     }">
                    <template x-if="isOvertime"><span class="text-3xl mr-2">+</span></template>
                    <span x-text="timer"></span>
                </div>
                <template x-if="isOvertime">
                    <div class="text-sm font-semibold text-[--status-danger] mt-2 animate-pulse">OVERTIME</div>
                </template>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8 p-4 md:p-6 bg-gray-50 rounded-xl border border-gray-200">
            <div>
                <div class="text-xs md:text-sm text-gray-500 mb-1 font-medium">Start Time</div>
                <div class="text-base md:text-lg font-bold text-slate-900" x-text="activeSession ? new Date(activeSession.starts_at).toLocaleTimeString() : ''"></div>
            </div>
            <div>
                <div class="text-xs md:text-sm text-gray-500 mb-1 font-medium">End Time</div>
                <div class="text-base md:text-lg font-bold text-slate-900" x-text="activeSession ? new Date(activeSession.ends_at).toLocaleTimeString() : ''"></div>
            </div>
            <div>
                <div class="text-xs md:text-sm text-gray-500 mb-1 font-medium">Duration</div>
                <div class="text-base md:text-lg font-bold text-slate-900" x-text="activeSession ? Math.floor((new Date(activeSession.ends_at) - new Date(activeSession.starts_at)) / (1000 * 60 * 60)) + ' hours' : ''"></div>
            </div>
            <div>
                <div class="text-xs md:text-sm text-gray-500 mb-1 font-medium">Current Bill</div>
                <div class="text-base md:text-lg font-bold text-[#ec4899]" x-text="'₱' + totalAmount.toLocaleString()"></div>
            </div>
        </div>

        <!-- Order Breakdown -->
        <template x-if="activeSession && activeSession.orders && activeSession.orders.some(o => o.items.length > 0)">
            <div class="mb-6 md:mb-8">
                <div class="flex items-center gap-2 mb-4">
                    <i data-lucide="shopping-bag" class="w-5 h-5 text-[#6366f1]"></i>
                    <h3 class="text-lg md:text-xl font-bold">Order Breakdown</h3>
                </div>
                <div class="bg-white border-2 border-gray-200 rounded-xl overflow-hidden">
                    <div class="max-h-64 overflow-y-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b-2 border-gray-200 sticky top-0">
                                <tr>
                                    <th class="text-left px-4 py-3 text-sm font-semibold text-gray-700">Item</th>
                                    <th class="text-center px-4 py-3 text-sm font-semibold text-gray-700">Qty</th>
                                    <th class="text-right px-4 py-3 text-sm font-semibold text-gray-700">Price</th>
                                    <th class="text-right px-4 py-3 text-sm font-semibold text-gray-700">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <template x-for="order in activeSession.orders" :key="order.id">
                                    <template x-for="item in order.items" :key="item.id">
                                        <tr class="hover:bg-gray-50">
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
                                    <td colspan="3" class="px-4 py-3 text-right font-bold text-gray-900">Total:</td>
                                    <td class="px-4 py-3 text-right font-bold text-xl text-[#ec4899]" x-text="'₱' + totalAmount.toLocaleString()"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </template>

        <div class="space-y-3">
            <div class="grid grid-cols-2 gap-3">
                <form :action="`{{ url('rooms/sessions') }}/${activeSession ? activeSession.id : ''}/extend`" method="POST">
                    @csrf
                    <input type="hidden" name="duration" value="30">
                    <button type="submit" class="w-full px-4 md:px-6 py-3 bg-[#3b82f6] text-white rounded-lg hover:bg-[#2563eb] active:scale-95 transition-all shadow-md flex items-center justify-center gap-2 font-medium">
                        <i data-lucide="plus" class="w-4 h-4 md:w-5 md:h-5"></i>
                        <span class="text-sm md:text-base">+30 mins</span>
                    </button>
                </form>
                <form :action="`{{ url('rooms/sessions') }}/${activeSession ? activeSession.id : ''}/extend`" method="POST">
                    @csrf
                    <input type="hidden" name="duration" value="60">
                    <button type="submit" class="w-full px-4 md:px-6 py-3 bg-[#3b82f6] text-white rounded-lg hover:bg-[#2563eb] active:scale-95 transition-all shadow-md flex items-center justify-center gap-2 font-medium">
                        <i data-lucide="plus" class="w-4 h-4 md:w-5 md:h-5"></i>
                        <span class="text-sm md:text-base">+1 hour</span>
                    </button>
                </form>
            </div>
            <button @click="showDetailModal = false; showOrdersModal = true"
                    class="w-full px-6 py-3.5 bg-[#6366f1] text-white rounded-lg hover:bg-[#5558e3] active:scale-95 transition-all shadow-md font-medium">
                Add Order
            </button>
            <button @click="showDetailModal = false; openBillOutModal(activeRoom, activeSession)"
                    class="w-full px-6 py-3.5 bg-[#ec4899] text-white rounded-lg hover:bg-[#db2777] active:scale-95 transition-all shadow-md font-medium">
                Bill Out
            </button>
        </div>
    </div>
</div>
