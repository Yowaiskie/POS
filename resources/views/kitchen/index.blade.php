@extends('layouts.app')

@section('content')
<div class="h-screen flex flex-col bg-gray-100" x-data="{
    init() {
        // Auto refresh every 15 seconds to fetch new orders
        // BUT we only reload if there are no ongoing interactions to avoid disrupting the user.
        // Actually, for simplicity, let's just keep the 15s reload. If they check a box, it saves instantly anyway.
        setInterval(() => {
            window.location.reload();
        }, 15000);
    }
}">
    <div class="bg-white p-4 shadow-sm border-b border-gray-200 flex justify-between items-center shrink-0">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <i data-lucide="chef-hat" class="w-8 h-8 text-orange-500"></i>
            Kitchen Dashboard
        </h1>
        <div class="text-sm text-gray-500 flex items-center gap-2">
            <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
            Auto-refreshing
        </div>
    </div>

    <div class="flex-1 overflow-auto p-6">
        @if($orders->isEmpty())
            <div class="h-full flex flex-col items-center justify-center text-gray-400">
                <i data-lucide="check-circle" class="w-16 h-16 mb-4 text-gray-300"></i>
                <p class="text-xl font-semibold">All caught up!</p>
                <p class="text-sm">No pending orders to prepare.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 items-start">
                @foreach($orders as $order)
                    @php
                        $pendingItems = $order->items->where('kitchen_status', 'pending');
                        $initialItems = $order->items->map(function($i) {
                            return ['id' => $i->id, 'served' => $i->kitchen_status === 'served'];
                        })->values()->toJson();
                    @endphp
                    @if($pendingItems->isNotEmpty())
                        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden flex flex-col relative"
                             x-data="{
                                items: {{ $initialItems }},
                                get allServed() { return this.items.length > 0 && this.items.every(i => i.served); },
                                async toggleItem(index) {
                                    this.items[index].served = !this.items[index].served;
                                    
                                    // Make API request in background
                                    try {
                                        await fetch(`/kitchen/serve/${this.items[index].id}`, {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json'
                                            },
                                            body: JSON.stringify({ served: this.items[index].served })
                                        });
                                    } catch (e) {
                                        console.error('Failed to update status');
                                    }
                                },
                                async completeAll() {
                                    this.items.forEach(i => i.served = true);
                                    
                                    try {
                                        await fetch(`/kitchen/serve-order/{{ $order->id }}`, {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json'
                                            }
                                        });
                                    } catch (e) {
                                        console.error('Failed to complete order');
                                    }
                                }
                             }"
                             x-show="!allServed"
                             x-transition:leave="transition ease-in duration-500"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-90">
                             
                            <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-start">
                                <div>
                                    <h3 class="font-bold text-lg text-gray-900">
                                        {{ $order->order_type === 'room' ? 'Room: ' . ($order->roomSession->room->name ?? 'N/A') : $order->transaction_id }}
                                    </h3>
                                    <div class="text-xs text-gray-500 mt-1">{{ $order->created_at->diffForHumans() }}</div>
                                </div>
                                <div class="flex flex-col gap-1.5 items-end">
                                    @if($order->location)
                                        <div class="px-3 py-1.5 rounded-md text-xs font-black uppercase tracking-wider shadow-sm
                                            {{ strtolower($order->location) === 'bar' ? 'bg-red-500 text-white' : 'bg-blue-500 text-white' }}">
                                            DESTINATION: {{ $order->location }}
                                        </div>
                                    @endif
                                    @if($order->dining_option)
                                        <div class="px-3 py-1.5 rounded-md text-[10px] font-bold uppercase tracking-wider border
                                            {{ strtolower($order->dining_option) === 'dine-in' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                            {{ str_replace('-', ' ', $order->dining_option) }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="p-0 flex-1">
                                <ul class="divide-y divide-gray-100">
                                    @foreach($order->items as $index => $item)
                                        <li class="p-4 flex items-center gap-4 hover:bg-gray-50 transition-colors cursor-pointer"
                                            @click="toggleItem({{ $index }})"
                                            :class="items[{{ $index }}].served ? 'bg-gray-50/50' : ''">
                                            
                                            <!-- Checkbox -->
                                            <div class="shrink-0 flex items-center justify-center w-6 h-6 rounded border-2 transition-colors duration-200"
                                                 :class="items[{{ $index }}].served ? 'bg-green-500 border-green-500 text-white' : 'border-gray-300 bg-white'">
                                                <svg x-show="items[{{ $index }}].served" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>

                                            <div class="flex items-center gap-3 flex-1">
                                                <div class="w-8 h-8 rounded flex items-center justify-center font-bold text-sm transition-colors duration-200"
                                                     :class="items[{{ $index }}].served ? 'bg-gray-200 text-gray-500' : 'bg-gray-100 text-gray-700'">
                                                    {{ $item->quantity }}x
                                                </div>
                                                <div class="font-semibold text-sm transition-all duration-200"
                                                     :class="items[{{ $index }}].served ? 'text-gray-400 line-through' : 'text-gray-800'">
                                                    {{ $item->name }}
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            
                            <!-- Complete Order Button -->
                            <div class="p-3 bg-white border-t border-gray-100 mt-auto">
                                <button @click="completeAll()"
                                        class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2">
                                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                                    Complete Entire Order
                                </button>
                            </div>
                            
                            <!-- Success overlay that shows briefly when all items are checked -->
                            <div x-show="allServed" 
                                 x-transition.opacity.duration.300ms
                                 class="absolute inset-0 bg-green-500/90 backdrop-blur-sm flex flex-col items-center justify-center text-white z-10" x-cloak>
                                <i data-lucide="check-circle" class="w-12 h-12 mb-2"></i>
                                <span class="font-bold text-lg">Order Complete!</span>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
