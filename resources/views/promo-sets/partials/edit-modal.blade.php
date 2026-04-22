<!-- Edit Promo Set Modal -->
<div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" 
     x-show="showEditModal" 
     x-transition.opacity 
     x-cloak
     style="display: none;">
    
    <div class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh] relative" 
         @click.away="showEditModal = false"
         x-show="showEditModal">
        
        <button @click="showEditModal = false" class="absolute top-4 right-4 p-2 hover:bg-gray-100 rounded-lg transition-colors z-10">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>

        <div class="p-6 md:p-8 border-b border-slate-100 bg-slate-50">
            <h2 class="text-2xl font-bold text-slate-900">Edit Promo Set</h2>
            <p class="text-sm text-slate-500 mt-1" x-text="activePromo ? 'Updating: ' + activePromo.name : ''"></p>
        </div>

        <form x-bind:action="activePromo ? `{{ url('promo-sets') }}/${activePromo.id}` : ''" method="POST" class="flex flex-col flex-1 overflow-hidden" x-show="activePromo">
            @csrf
            @method('PUT')
            
            <div class="p-6 md:p-8 overflow-y-auto space-y-6">
                <!-- Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Promo Name</label>
                        <input type="text" name="name" required x-model="activePromo.name" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Price (₱)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-600 font-semibold">₱</span>
                            <input type="number" name="price" required min="0" x-model="activePromo.price" class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:border-indigo-500 focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Room Duration (Hours)</label>
                        <input type="number" name="duration_hours" required min="1" x-model="activePromo.duration_hours" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div class="md:col-span-2 flex items-center gap-3 p-4 bg-slate-50 rounded-xl border-2 border-gray-200">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1" x-model="activePromo.is_active" class="w-5 h-5 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                        <label for="edit_is_active" class="text-sm font-bold text-slate-700 cursor-pointer">Active and Visible</label>
                    </div>
                </div>

                <!-- Inclusions -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-sm font-semibold text-slate-700">Included Items</label>
                        <button type="button" @click="activePromo.items.push({ menu_item_id: '', quantity: 1 })" class="text-sm text-[#6366f1] font-bold hover:text-indigo-800 flex items-center gap-1">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Add Item
                        </button>
                    </div>
                    
                    <div class="space-y-3">
                        <template x-for="(item, index) in activePromo.items" :key="index">
                            <div class="flex gap-3 items-end bg-slate-50 p-4 rounded-xl border-2 border-gray-200">
                                <div class="flex-1">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Menu Item</label>
                                    <select :name="`items[${index}][menu_item_id]`" required x-model="item.menu_item_id" class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-indigo-500 focus:outline-none text-sm">
                                        <option value="">Select an item</option>
                                        @foreach($menuItems as $menuItem)
                                            <option value="{{ $menuItem->id }}">{{ $menuItem->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-24">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Qty</label>
                                    <input type="number" :name="`items[${index}][quantity]`" required min="1" x-model="item.quantity" class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-indigo-500 focus:outline-none text-sm">
                                </div>
                                <button type="button" @click="activePromo.items.splice(index, 1)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" x-show="activePromo.items.length > 1">
                                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            
            <div class="p-6 md:p-8 border-t border-slate-100 bg-slate-50 flex gap-3">
                <button type="button" @click="showEditModal = false" class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 active:scale-95 transition-all font-medium">Cancel</button>
                <button type="submit" class="flex-1 px-6 py-3 bg-[#6366f1] text-white rounded-lg hover:bg-[#5558e3] active:scale-95 transition-all shadow-md font-medium">Save Changes</button>
            </div>
        </form>
    </div>
</div>

