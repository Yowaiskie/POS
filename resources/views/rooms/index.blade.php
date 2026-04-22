@extends('layouts.app')

@section('content')
<div class="p-4 md:p-8 max-w-[1600px] mx-auto" 
     x-data="{ 
        showDetailModal: false,
        showOrdersModal: false,
        showBillOutModal: false,
        activeSession: null,
        activeRoom: null,
        selectedCategory: '{{ $categories->first()?->slug }}',
        paymentMethod: null,
        transactionNumber: '',
        amountReceived: '',
        
        openDetailModal(room, session) {
            this.activeRoom = room;
            this.activeSession = session;
            this.showDetailModal = true;
        },

        openOrdersModal(room, session) {
            this.activeRoom = room;
            this.activeSession = session;
            this.showOrdersModal = true;
        },

        openBillOutModal(room, session) {
            this.activeRoom = room;
            this.activeSession = session;
            this.paymentMethod = null;
            this.transactionNumber = '';
            this.amountReceived = '';
            this.showBillOutModal = true;
        },

        get receivedAmount() {
            return parseFloat(this.amountReceived) || 0;
        },

        get totalAmount() {
            if (!this.activeSession) return 0;
            return this.activeSession.orders.reduce((t, o) => t + o.items.reduce((it, i) => it + (i.unit_price * i.quantity), 0), 0);
        },

        get change() {
            return this.receivedAmount - this.totalAmount;
        }
     }"
     x-init='
        @if(session("open_modal_for_session"))
            @php
                $openedSession = \App\Models\RoomSession::with("room", "orders.items")->find(session("open_modal_for_session"));
            @endphp
            @if($openedSession)
                openOrdersModal(@json($openedSession->room), @json($openedSession))
            @endif
        @endif
     '>
    
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-2">Manage Rooms</h1>
        <p class="text-slate-600">Monitor and control all KTV room sessions</p>
    </div>

    @php
        $activeRooms = $rooms->filter(fn($r) => $r->activeSession);
        $availableRooms = $rooms->filter(fn($r) => !$r->activeSession);
    @endphp

    <!-- Active Sessions Section -->
    @if($activeRooms->isNotEmpty())
    <div class="mb-10">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-1 h-6 bg-gradient-to-b from-emerald-500 to-teal-500 rounded-full"></div>
            <h2 class="text-xl md:text-2xl font-bold text-slate-900">Active Sessions</h2>
            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-sm font-semibold rounded-full">
                {{ $activeRooms->count() }}
            </span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($activeRooms as $room)
                @include('rooms.partials.room-card', ['room' => $room])
            @endforeach
        </div>
    </div>
    @endif

    <!-- Available Rooms Section -->
    @if($availableRooms->isNotEmpty())
    <div>
        <div class="flex items-center gap-3 mb-5">
            <div class="w-1 h-6 bg-gradient-to-b from-slate-400 to-slate-500 rounded-full"></div>
            <h2 class="text-xl md:text-2xl font-bold text-slate-900">Available Rooms</h2>
            <span class="px-3 py-1 bg-slate-100 text-slate-600 text-sm font-semibold rounded-full">
                {{ $availableRooms->count() }}
            </span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($availableRooms as $room)
                @include('rooms.partials.room-card', ['room' => $room])
            @endforeach
        </div>
    </div>
    @endif

    <!-- Modals -->
    @include('rooms.partials.room-detail-modal')
    @include('rooms.partials.orders-modal')
    @include('rooms.partials.bill-out-modal')
</div>
@endsection
