{{-- Start Shift Modal --}}
<div x-data="{ showStartShiftModal: false }"
     @open-start-shift-modal.window="showStartShiftModal = true"
     x-show="showStartShiftModal"
     style="display: none;"
     class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
    
    <div x-show="showStartShiftModal"
         @click.away="showStartShiftModal = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4"
         class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden flex flex-col">
        
        <div class="p-6 border-b border-gray-100 bg-gradient-to-br from-green-50 to-white relative">
            <button @click="showStartShiftModal = false" class="absolute top-4 right-4 p-2 text-gray-400 hover:text-gray-600 hover:bg-white rounded-full transition-colors shadow-sm">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
            <div class="w-12 h-12 bg-green-100 text-green-600 rounded-xl flex items-center justify-center mb-4 shadow-sm border border-green-200">
                <i data-lucide="clock-arrow-up" class="w-6 h-6"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-1">Start Your Shift</h2>
            <p class="text-sm text-gray-500">Please enter your starting cash float (panukli) to begin transactions.</p>
        </div>

        <form action="{{ route('shifts.start') }}" method="POST" class="p-6 flex flex-col gap-5">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Starting Cash Float (₱)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-500 font-medium text-lg">₱</span>
                    <input type="number" name="starting_cash" step="0.01" required min="0" value="0"
                           class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all font-mono text-xl text-gray-900 shadow-inner"
                           placeholder="0.00">
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" @click="showStartShiftModal = false" class="flex-1 px-4 py-2.5 text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 font-semibold rounded-xl transition-all shadow-sm">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 text-white bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 font-semibold rounded-xl transition-all shadow-md flex items-center justify-center gap-2">
                    <i data-lucide="play" class="w-4 h-4"></i> Start Shift
                </button>
            </div>
        </form>
    </div>
</div>

{{-- End Shift Modal (Blind Drop) --}}
<div x-data="{ showEndShiftModal: false }"
     @open-end-shift-modal.window="showEndShiftModal = true"
     x-show="showEndShiftModal"
     style="display: none;"
     class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
    
    <div x-show="showEndShiftModal"
         @click.away="showEndShiftModal = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4"
         class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden flex flex-col">
        
        <div class="p-6 border-b border-gray-100 bg-gradient-to-br from-red-50 to-white relative">
            <button @click="showEndShiftModal = false" class="absolute top-4 right-4 p-2 text-gray-400 hover:text-gray-600 hover:bg-white rounded-full transition-colors shadow-sm">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
            <div class="w-12 h-12 bg-red-100 text-red-600 rounded-xl flex items-center justify-center mb-4 shadow-sm border border-red-200">
                <i data-lucide="clock-arrow-down" class="w-6 h-6"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-1">End Your Shift</h2>
            <p class="text-sm text-red-600 font-medium bg-red-50 px-3 py-2 rounded-lg border border-red-100 inline-block mt-1">Blind Drop Mode Active</p>
            <p class="text-xs text-gray-500 mt-2">Count your cash drawer and enter the exact total amount. The system will calculate differences afterward.</p>
        </div>

        <form action="{{ route('shifts.end') }}" method="POST" class="p-6 flex flex-col gap-5">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Total Cash Counted (₱)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-500 font-medium text-lg">₱</span>
                    <input type="number" name="actual_cash" step="0.01" required min="0"
                           class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all font-mono text-xl text-gray-900 shadow-inner"
                           placeholder="0.00">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center justify-between">
                    <span>Total GCash Received (₱)</span>
                    <i data-lucide="smartphone" class="w-4 h-4 text-blue-500"></i>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-500 font-medium text-lg">₱</span>
                    <input type="number" name="actual_gcash" step="0.01" required min="0"
                           class="w-full pl-10 pr-4 py-3 bg-blue-50 border border-blue-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-mono text-xl text-blue-900 shadow-inner"
                           placeholder="0.00">
                </div>
                <p class="text-[10px] text-gray-500 mt-1.5 leading-tight">Check your device/terminal history for the total GCash payments received during your shift.</p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" @click="showEndShiftModal = false" class="flex-1 px-4 py-2.5 text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 font-semibold rounded-xl transition-all shadow-sm">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 text-white bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 font-semibold rounded-xl transition-all shadow-md flex items-center justify-center gap-2">
                    <i data-lucide="check-square" class="w-4 h-4"></i> Submit & End
                </button>
            </div>
        </form>
    </div>
</div>
