<!-- Admin PIN Verification Modal (Sleek Box Version) -->
<div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-[100] flex items-center justify-center p-4" 
     x-show="showPinModal" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100"
     x-cloak>
    
    <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full overflow-hidden relative border border-slate-200" 
         @click.away="closePinModal()">
        
        <div class="p-8 text-center">
            <div class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
                <i data-lucide="shield-check" class="w-8 h-8"></i>
            </div>
            
            <h3 class="text-xl font-black text-slate-800 mb-1 uppercase tracking-tight">Manager Approval</h3>
            <p class="text-sm text-slate-500 mb-8 font-medium">Please enter Admin security PIN</p>

            {{-- PIN Input Box --}}
            <div class="mb-4 relative">
                <input type="password" 
                       x-model="pinValue"
                       x-ref="pinInput"
                       @keydown.enter="verifyPin()"
                       maxlength="6"
                       placeholder="••••"
                       class="w-full h-20 text-center text-4xl font-black tracking-[0.5em] text-indigo-600 bg-slate-50 border-2 border-slate-200 rounded-2xl focus:border-indigo-500 focus:bg-white focus:outline-none transition-all placeholder:text-slate-200 shadow-inner">
            </div>

            <template x-if="pinError">
                <div class="mb-6 text-red-500 text-xs font-bold uppercase tracking-wider animate-pulse" x-text="pinError"></div>
            </template>

            <div class="grid grid-cols-2 gap-3">
                <button @click="closePinModal()" 
                        class="py-4 bg-slate-100 text-slate-600 rounded-2xl font-bold uppercase tracking-widest text-xs hover:bg-slate-200 transition-all active:scale-95">
                    Cancel
                </button>
                <button @click="verifyPin()" 
                        :disabled="pinValue.length < 4 || isVerifyingPin"
                        class="py-4 bg-indigo-600 text-white disabled:bg-slate-300 rounded-2xl font-bold uppercase tracking-widest text-xs shadow-lg shadow-indigo-200 active:scale-95 transition-all">
                    Authorize
                </button>
            </div>
        </div>

        {{-- Processing Overlay --}}
        <div class="absolute inset-0 bg-white/80 backdrop-blur-[2px] flex flex-col items-center justify-center z-10" 
             x-show="isVerifyingPin" x-cloak>
            <div class="relative w-12 h-12">
                <div class="absolute inset-0 border-4 border-indigo-100 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-indigo-600 rounded-full border-t-transparent animate-spin"></div>
            </div>
            <span class="mt-4 text-xs font-bold text-indigo-600 uppercase tracking-widest">Verifying PIN...</span>
        </div>
    </div>
</div>
