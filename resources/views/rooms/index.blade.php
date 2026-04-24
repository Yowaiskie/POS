@extends('layouts.app')

@section('content')
<div class="p-4 md:p-8 max-w-[1600px] mx-auto" x-data="roomManager">
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
    @include('rooms.partials.start-session-modal')
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('roomManager', () => ({
        showDetailModal: false,
        showOrdersModal: false,
        showBillOutModal: false,
        showStartSessionModal: false,
        isProcessing: false,
        activeSession: null,
        activeRoom: null,
        isUnlocked: false,
        selectedCategory: '{{ $categories->first()?->slug }}',
        paymentMethod: null,
        transactionNumber: '',
        amountReceived: '',
        pricingConfig: {!! json_encode($pricing ?? [
            'price_30_min' => 100.0,
            'price_60_min' => 350.0,
            'overtime_unit_minutes' => 10,
            'overtime_unit_price' => 50.0,
            'grace_period_minutes' => 10
        ]) !!},

        init() {
            @if(session("open_modal_for_session"))
                @php
                    $openedSession = \App\Models\RoomSession::with("room", "orders.items")->find(session("open_modal_for_session"));
                @endphp
                @if($openedSession)
                    this.openOrdersModal(@json($openedSession->room), @json($openedSession));
                @endif
            @endif
        },
        
        openStartSessionModal(room) {
            this.activeRoom = room;
            this.showStartSessionModal = true;
        },

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

        async submitOrderForm(e) {
            const form = e.target;
            const formData = new FormData(form);
            
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Update the active session with new order data
                    this.activeSession = data.session;
                    // Refresh icons after DOM update
                    this.$nextTick(() => {
                        if (typeof lucide !== 'undefined') lucide.createIcons();
                    });
                } else {
                    alert(data.message || 'Error adding item');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        },

        async updateItemQuantity(item, newQty) {
            newQty = parseInt(newQty);
            if (isNaN(newQty) || newQty < 1) return;
            
            const currentQty = parseInt(item.quantity);
            
            // If already unlocked, just submit
            if (this.isUnlocked || newQty > currentQty) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('orders/update-quantity') }}/${item.id}`;
                
                const token = document.createElement('input');
                token.type = 'hidden'; token.name = '_token'; token.value = '{{ csrf_token() }}';
                form.appendChild(token);
                
                const qtyInput = document.createElement('input');
                qtyInput.type = 'hidden'; qtyInput.name = 'quantity'; qtyInput.value = newQty;
                form.appendChild(qtyInput);

                // If unlocked, we might still need an admin_id for the audit log
                // Let's just pass a flag or a dummy if needed, or update backend to assume auth
                const adminInput = document.createElement('input');
                adminInput.type = 'hidden'; adminInput.name = 'admin_id'; adminInput.value = '1';
                form.appendChild(adminInput);
                
                document.body.appendChild(form);
                form.submit();
                return;
            }

            // Otherwise, ask for PIN
            this.openPinModal(async (adminId) => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('orders/update-quantity') }}/${item.id}`;
                
                const token = document.createElement('input');
                token.type = 'hidden'; token.name = '_token'; token.value = '{{ csrf_token() }}';
                form.appendChild(token);
                
                const qtyInput = document.createElement('input');
                qtyInput.type = 'hidden'; qtyInput.name = 'quantity'; qtyInput.value = newQty;
                form.appendChild(qtyInput);

                const adminInput = document.createElement('input');
                adminInput.type = 'hidden'; adminInput.name = 'admin_id'; adminInput.value = adminId;
                form.appendChild(adminInput);
                
                document.body.appendChild(form);
                form.submit();
            });
        },

        unlockManagerMode() {
            this.openPinModal((adminId) => {
                this.isUnlocked = true;
                this.$nextTick(() => {
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                });
            });
        },

        get roomBilling() {
            if (!this.activeSession || !this.activeSession.started_at || !this.activeSession.ends_at) {
                return { extension: 0, overtime: 0, total: 0, extensionDesc: '', overtimeDesc: '', reserved_hours: 0 };
            }
            
            const startStr = String(this.activeSession.started_at).replace(' ', 'T');
            const endStr = String(this.activeSession.ends_at).replace(' ', 'T');
            const start = new Date(startStr);
            const reservedEnd = new Date(endStr);
            const now = new Date();
            
            if (isNaN(start.getTime()) || isNaN(reservedEnd.getTime())) {
                return { extension: 0, overtime: 0, total: 0, extensionDesc: '', overtimeDesc: '', reserved_hours: 0 };
            }

            const promoMinutes = parseFloat(this.activeSession.promo_duration_hours || 0) * 60;
            
            // 1. Calculate Extension (Reserved Time - Promo Time)
            const totalReservedMinutes = Math.max(0, Math.floor((reservedEnd - start) / 60000));
            const extensionMinutes = Math.max(0, totalReservedMinutes - promoMinutes);
            
            // 2. Calculate Overtime (Actual Time stayed beyond Reserved End)
            const overtimeMinutes = now > reservedEnd ? Math.floor((now - reservedEnd) / 60000) : 0;
            
            const calcCharge = (mins) => {
                if (mins <= 0) return { charge: 0, desc: '' };
                const h = Math.floor(mins / 60);
                const m = mins % 60;
                let charge = h * parseFloat(this.pricingConfig.price_60_min);
                let parts = [];
                if (h > 0) parts.push(h + ' hr(s)');
                
                if (m > 0) {
                    if (m >= 30) {
                        const m30Price = parseFloat(this.pricingConfig.price_30_min);
                        const otAfter30 = m - 30;
                        if (otAfter30 > 0) {
                            const units = Math.ceil(otAfter30 / this.pricingConfig.overtime_unit_minutes);
                            const extra = units * parseFloat(this.pricingConfig.overtime_unit_price);
                            charge += Math.min(m30Price + extra, parseFloat(this.pricingConfig.price_60_min));
                            parts.push('30m block + OT');
                        } else {
                            charge += m30Price;
                            parts.push('30m block');
                        }
                    } else {
                        const units = Math.ceil(m / this.pricingConfig.overtime_unit_minutes);
                        const looseCharge = units * parseFloat(this.pricingConfig.overtime_unit_price);
                        charge += Math.min(looseCharge, parseFloat(this.pricingConfig.price_30_min));
                        parts.push(m + 'm extra');
                    }
                }
                return { charge: charge, desc: parts.join(' + ') };
            };

            const extResult = calcCharge(extensionMinutes);
            const otResult = calcCharge(overtimeMinutes);
            
            return {
                extension: extResult.charge,
                overtime: otResult.charge,
                total: extResult.charge + otResult.charge,
                extensionDesc: extResult.desc,
                overtimeDesc: otResult.desc,
                reserved_hours: totalReservedMinutes / 60,
                total_minutes: extensionMinutes + overtimeMinutes
            };
        },

        get roomCharge() {
            return this.roomBilling.total;
        },

        get receivedAmount() {
            return parseFloat(this.amountReceived) || 0;
        },

        get totalAmount() {
            if (!this.activeSession) return 0;
            const foodTotal = this.activeSession.orders.reduce((t, o) => {
                return t + (o.items || []).reduce((it, i) => it + (parseFloat(i.unit_price) * parseInt(i.quantity)), 0);
            }, 0);
            return foodTotal + this.roomCharge;
        },

        get change() {
            return this.receivedAmount - this.totalAmount;
        }
    }));
});
</script>
@endpush
@endsection
