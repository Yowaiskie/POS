@extends('layouts.app')

@section('content')
<div class="max-w-[1600px] mx-auto" 
     x-data="{ 
        showOrdersModal: false,
        activeSession: null,
        activeRoom: null,
        selectedCategory: '{{ $categories->first()?->slug }}',
        
        openOrdersModal(room, session) {
            this.activeRoom = room;
            this.activeSession = session;
            this.showOrdersModal = true;
        }
     }"
     x-init="
        @if(session('open_modal_for_session'))
            @php
                $openedSession = \App\Models\RoomSession::with('room', 'orders.items')->find(session('open_modal_for_session'));
            @endphp
            @if($openedSession)
                openOrdersModal(@json($openedSession->room), @json($openedSession))
            @endif
        @endif
     ">
    
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-2">Manage Rooms</h1>
                <p class="text-slate-600">Monitor and control all KTV room sessions</p>
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <div class="flex items-center gap-4 bg-white px-4 py-2 rounded-lg border border-slate-200 shadow-sm overflow-x-auto">
                    <div class="flex items-center gap-2 whitespace-nowrap">
                        <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                        <span class="text-xs font-semibold text-slate-600">Active</span>
                    </div>
                    <div class="flex items-center gap-2 whitespace-nowrap">
                        <span class="w-3 h-3 rounded-full bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]"></span>
                        <span class="text-xs font-semibold text-slate-600">Warning</span>
                    </div>
                    <div class="flex items-center gap-2 whitespace-nowrap">
                        <span class="w-3 h-3 rounded-full bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.5)]"></span>
                        <span class="text-xs font-semibold text-slate-600">Overtime</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @forelse($rooms as $room)
            @php
                $session = $room->activeSession;
                $status = $session?->status ?? 'available';
                $bill = $session ? $session->orders->sum('total_amount') : 0;
            @endphp
            <div class="bg-white rounded-xl border-2 p-6 shadow-sm hover:shadow-lg transition-all duration-200 h-full flex flex-col group relative overflow-hidden"
                 x-data="{ 
                    endTime: '{{ $session?->ends_at?->toIso8601String() }}',
                    timer: '00:00:00',
                    isOvertime: false,
                    status: '{{ $status }}',
                    updateTimer() {
                        if (!this.endTime) return;
                        const end = new Date(this.endTime);
                        const now = new Date();
                        let diff = Math.abs(end - now) / 1000;
                        this.isOvertime = now > end;
                        
                        const h = Math.floor(diff / 3600);
                        const m = Math.floor((diff % 3600) / 60);
                        const s = Math.floor(diff % 60);
                        this.timer = (this.isOvertime ? '+' : '') + 
                                     [h, m, s].map(v => v.toString().padStart(2, '0')).join(':');

                        if (this.isOvertime) this.status = 'overtime';
                        else if (diff < 600) this.status = 'warning';
                        else this.status = 'active';
                    }
                 }"
                 x-init="if(endTime) { updateTimer(); setInterval(() => updateTimer(), 1000) }"
                 :class="{
                    'border-emerald-200': status === 'active',
                    'border-amber-200': status === 'warning',
                    'border-rose-200': status === 'overtime',
                    'border-slate-100': status === 'available'
                 }">
                
                <template x-if="status !== 'available'">
                    <div class="absolute top-0 right-0 p-2 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i data-lucide="mic-2" class="w-16 h-16 -rotate-12" :class="{
                            'text-emerald-600': status === 'active',
                            'text-amber-600': status === 'warning',
                            'text-rose-600': status === 'overtime'
                        }"></i>
                    </div>
                </template>

                <div class="flex items-start justify-between mb-4 relative z-10">
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-slate-900 mb-3">{{ $room->name }}</h3>
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold border uppercase tracking-wider transition-colors"
                              :class="{
                                'bg-emerald-50 text-emerald-700 border-emerald-200': status === 'active',
                                'bg-amber-50 text-amber-700 border-amber-200': status === 'warning',
                                'bg-rose-50 text-rose-700 border-rose-200': status === 'overtime',
                                'bg-slate-50 text-slate-600 border-slate-200': status === 'available'
                              }">
                            <span class="w-2 h-2 rounded-full" :class="{
                                'bg-emerald-500 animate-pulse': status === 'active',
                                'bg-amber-500 animate-bounce': status === 'warning',
                                'bg-rose-500 animate-ping': status === 'overtime',
                                'bg-slate-400': status === 'available'
                            }"></span>
                            <span x-text="status"></span>
                        </span>
                    </div>
                </div>

                <div class="flex-1 flex flex-col justify-center py-6 bg-slate-50 rounded-xl mb-4 relative z-10">
                    <div class="text-center">
                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mb-1">Remaining Time</div>
                        <div class="font-mono text-3xl font-black tracking-tighter transition-colors"
                             :class="{
                                'text-emerald-600': status === 'active',
                                'text-amber-600': status === 'warning',
                                'text-rose-600': status === 'overtime',
                                'text-slate-300': status === 'available'
                             }"
                             x-text="status === 'available' ? '00:00:00' : timer">
                        </div>
                    </div>
                    @if($status !== 'available')
                    <div class="mt-4 pt-4 border-t border-slate-200/50 text-center">
                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mb-1">Current Bill</div>
                        <div class="text-2xl font-black text-slate-900 tracking-tight">₱{{ number_format($bill) }}</div>
                    </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 gap-2.5 relative z-10">
                    @if($status === 'available')
                        <form action="{{ route('rooms.start-session', $room) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white rounded-lg hover:from-indigo-700 hover:to-indigo-600 active:scale-98 transition-all font-bold shadow-md shadow-indigo-200">
                                Start Session
                            </button>
                        </form>
                    @else
                        <div class="flex gap-2">
                            <button @click="openOrdersModal(@json($room), @json($session))" 
                                    class="flex-1 px-4 py-2.5 bg-white border-2 border-slate-200 text-slate-700 rounded-lg hover:border-indigo-600 hover:text-indigo-600 active:scale-95 transition-all text-sm font-bold text-center">
                                Orders
                            </button>
                            <form action="{{ route('rooms.extend-session', $session) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 active:scale-95 transition-all text-sm font-bold shadow-md">
                                    Extend
                                </button>
                            </form>
                        </div>
                        <form action="{{ route('rooms.bill-out', $session) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2.5 bg-slate-900 text-white rounded-lg hover:bg-black active:scale-95 transition-all text-sm font-bold mt-1">
                                Bill Out
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border-2 border-slate-200 p-6 text-center text-slate-500">
                No rooms available.
            </div>
        @endforelse
    </div>

    <!-- Room Orders Modal -->
    <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4" x-show="showOrdersModal" x-transition x-cloak>
        <div class="bg-white rounded-3xl overflow-hidden max-w-6xl w-full h-[90vh] flex flex-col relative shadow-2xl" @click.away="showOrdersModal = false">
            <!-- Modal Header -->
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight" x-text="activeRoom ? activeRoom.name + ' - Room Orders' : 'Room Orders'"></h2>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Manage food and drinks for this session</p>
                </div>
                <button @click="showOrdersModal = false" class="p-2 hover:bg-white rounded-xl transition-all shadow-sm border border-transparent hover:border-slate-200">
                    <i data-lucide="x" class="w-6 h-6 text-slate-400"></i>
                </button>
            </div>

            <div class="flex-1 flex flex-col lg:flex-row overflow-hidden">
                <!-- Left: Menu Selection -->
                <div class="flex-1 flex flex-col border-r border-slate-100 bg-white overflow-hidden">
                    <!-- Categories -->
                    <div class="p-4 border-b border-slate-50 flex gap-2 overflow-x-auto shrink-0">
                        @foreach($categories as $cat)
                            <button @click="selectedCategory = '{{ $cat->slug }}'" 
                                    class="flex-shrink-0 px-4 py-2 rounded-xl text-sm font-bold transition-all"
                                    :class="selectedCategory === '{{ $cat->slug }}' ? 'bg-indigo-600 text-white shadow-lg' : 'bg-slate-50 text-slate-500 hover:bg-slate-100'">
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Items Grid -->
                    <div class="flex-1 overflow-y-auto p-4 md:p-6 bg-slate-50/30">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($items as $item)
                                <form action="{{ route('orders.add-item') }}" method="POST" x-show="selectedCategory === '{{ $item->category->slug }}'" x-transition>
                                    @csrf
                                    <input type="hidden" name="menu_item_id" value="{{ $item->id }}">
                                    <input type="hidden" name="room_session_id" :value="activeSession ? activeSession.id : ''">
                                    <button type="submit" class="w-full bg-white border border-slate-200 rounded-2xl p-4 hover:border-indigo-400 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 text-left group min-h-[120px] flex flex-col">
                                        <h3 class="font-bold text-slate-900 leading-snug line-clamp-2 flex-1 group-hover:text-indigo-600 transition-colors text-sm">
                                            {{ $item->name }}
                                        </h3>
                                        <div class="text-lg font-black bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">₱{{ number_format($item->price) }}</div>
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right: Cart Summary -->
                <div class="w-full lg:w-96 bg-white flex flex-col overflow-hidden">
                    <div class="p-4 border-b border-slate-50 flex items-center gap-2">
                        <i data-lucide="shopping-cart" class="w-5 h-5 text-indigo-600"></i>
                        <h3 class="font-bold text-slate-900">Current Order</h3>
                    </div>

                    <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50/30">
                        <template x-if="activeSession && activeSession.orders">
                            <template x-for="order in activeSession.orders" :key="order.id">
                                <div class="space-y-3">
                                    <template x-for="item in order.items" :key="item.id">
                                        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm">
                                            <div class="flex justify-between items-start mb-3">
                                                <div class="flex-1 pr-2">
                                                    <div class="font-bold text-slate-900 text-sm" x-text="item.name"></div>
                                                    <div class="text-[10px] font-bold text-slate-400" x-text="'₱' + Number(item.unit_price).toLocaleString() + ' each'"></div>
                                                </div>
                                                <div class="text-sm font-black text-rose-500" x-text="'₱' + (item.unit_price * item.quantity).toLocaleString()"></div>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <form :action="`{{ url('orders/update-quantity') }}/${item.id}`" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="delta" value="-1">
                                                    <button type="submit" class="w-8 h-8 flex items-center justify-center bg-slate-100 hover:bg-slate-200 rounded-lg transition-all text-slate-600">
                                                        <i data-lucide="minus" class="w-3 h-3"></i>
                                                    </button>
                                                </form>
                                                <div class="flex-1 text-center font-black text-slate-900" x-text="item.quantity"></div>
                                                <form :action="`{{ url('orders/update-quantity') }}/${item.id}`" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="delta" value="1">
                                                    <button type="submit" class="w-8 h-8 flex items-center justify-center bg-indigo-600 text-white hover:bg-indigo-700 rounded-lg transition-all shadow-md">
                                                        <i data-lucide="plus" class="w-3 h-3"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </template>
                        <template x-if="!activeSession || !activeSession.orders || activeSession.orders.length === 0 || activeSession.orders.every(o => o.items.length === 0)">
                            <div class="h-full flex flex-col items-center justify-center text-center p-8 opacity-40">
                                <i data-lucide="utensils" class="w-12 h-12 mb-4"></i>
                                <p class="text-sm font-bold text-slate-500">No items added yet</p>
                            </div>
                        </template>
                    </div>

                    <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                        <div class="flex justify-between items-center text-xl font-black text-slate-900 mb-4">
                            <span>Total Bill</span>
                            <span class="text-rose-500" x-text="'₱' + (activeSession && activeSession.orders ? activeSession.orders.reduce((t, o) => t + o.items.reduce((it, i) => it + (i.unit_price * i.quantity), 0), 0) : 0).toLocaleString()"></span>
                        </div>
                        <button @click="showOrdersModal = false" class="w-full px-6 py-4 bg-slate-900 text-white rounded-2xl hover:bg-black active:scale-95 transition-all font-black shadow-xl">
                            Done
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
