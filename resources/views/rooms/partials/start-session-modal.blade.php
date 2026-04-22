<!-- Start Session Modal -->
<div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" 
     x-show="showStartSessionModal" 
     x-transition.opacity 
     x-cloak
     style="display: none;">
    
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden flex flex-col max-h-[90vh]" 
         @click.away="showStartSessionModal = false"
         x-show="showStartSessionModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <div>
                <h3 class="text-xl font-bold text-slate-900" x-text="activeRoom ? 'Start Session: ' + activeRoom.name : 'Start Session'"></h3>
                <p class="text-sm text-slate-500 mt-1">Select a promo set or start a regular session</p>
            </div>
            <button @click="showStartSessionModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-200 p-2 rounded-xl transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form x-bind:action="activeRoom ? `{{ url('rooms') }}/${activeRoom.id}/start` : ''" method="POST" class="flex flex-col flex-1 overflow-hidden" x-data="{ selectedPromo: '' }">
            @csrf
            
            <div class="p-6 overflow-y-auto space-y-4">
                
                <!-- Regular Session Option -->
                <label class="block cursor-pointer">
                    <input type="radio" name="promo_set_id" value="" x-model="selectedPromo" class="sr-only">
                    <div class="p-4 rounded-xl border-2 transition-all"
                         :class="selectedPromo === '' ? 'border-indigo-600 bg-indigo-50' : 'border-slate-200 hover:bg-slate-50'">
                        <div class="flex items-center justify-between mb-2">
                            <div class="font-bold text-lg text-slate-900 flex items-center gap-2">
                                <i data-lucide="clock" class="w-5 h-5 text-indigo-600"></i>
                                Regular Session
                            </div>
                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors"
                                 :class="selectedPromo === '' ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300'">
                                <div class="w-2 h-2 rounded-full bg-white" x-show="selectedPromo === ''"></div>
                            </div>
                        </div>
                        <p class="text-sm text-slate-500">Standard 1-hour room duration without included items.</p>
                        
                        <div class="mt-4" x-show="selectedPromo === ''" x-transition>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Duration (Hours)</label>
                            <input type="number" name="duration" value="1" min="1" max="12" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </label>

                <!-- Promo Sets Options -->
                @foreach($promoSets as $promo)
                <label class="block cursor-pointer">
                    <input type="radio" name="promo_set_id" value="{{ $promo->id }}" x-model="selectedPromo" class="sr-only">
                    <div class="p-4 rounded-xl border-2 transition-all"
                         :class="selectedPromo == '{{ $promo->id }}' ? 'border-emerald-500 bg-emerald-50' : 'border-slate-200 hover:bg-slate-50'">
                        <div class="flex items-center justify-between mb-2">
                            <div class="font-bold text-lg text-slate-900 flex items-center gap-2">
                                <i data-lucide="sparkles" class="w-5 h-5 text-amber-500"></i>
                                {{ $promo->name }}
                            </div>
                            <div class="text-right flex items-center gap-3">
                                <span class="font-black text-emerald-600">₱{{ number_format($promo->price) }}</span>
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors"
                                     :class="selectedPromo == '{{ $promo->id }}' ? 'border-emerald-500 bg-emerald-500' : 'border-slate-300'">
                                    <div class="w-2 h-2 rounded-full bg-white" x-show="selectedPromo == '{{ $promo->id }}'"></div>
                                </div>
                            </div>
                        </div>
                        <p class="text-sm font-semibold text-slate-600 mb-3">{{ $promo->duration_hours }} hrs of Room Use</p>
                        
                        <div class="space-y-1">
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Inclusions</div>
                            <div class="grid grid-cols-2 gap-2 text-sm text-slate-600">
                                @foreach($promo->items as $item)
                                <div class="flex items-center gap-1.5">
                                    <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-500 shrink-0"></i>
                                    <span class="truncate">{{ $item->quantity }}x {{ $item->menuItem->name ?? 'Unknown Item' }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </label>
                @endforeach

            </div>
            
            <div class="p-6 border-t border-slate-100 bg-slate-50 flex gap-3">
                <button type="button" @click="showStartSessionModal = false" class="flex-1 px-4 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 active:scale-95 transition-all font-semibold">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 active:scale-95 transition-all font-semibold shadow-md flex justify-center items-center gap-2">
                    <i data-lucide="play" class="w-4 h-4"></i>
                    Start Now
                </button>
            </div>
        </form>
    </div>
</div>
