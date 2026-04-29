<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BOSSTON - KTV POS System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="antialiased" x-data="{
    sidebarCollapsed: JSON.parse(localStorage.getItem('sidebarCollapsed') ?? 'false'),
    notifications: [],
    
    // PIN Modal State
    showPinModal: false,
    pinValue: '',
    pinError: '',
    isVerifyingPin: false,
    pinCallback: null,

    toggleSidebar() {
        this.sidebarCollapsed = !this.sidebarCollapsed;
        localStorage.setItem('sidebarCollapsed', JSON.stringify(this.sidebarCollapsed));
    },

    openPinModal(callback) {
        this.pinValue = '';
        this.pinError = '';
        this.pinCallback = callback;
        this.showPinModal = true;
        this.$nextTick(() => {
            if (this.$refs.pinInput) {
                this.$refs.pinInput.focus();
            }
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    },

    closePinModal() {
        this.showPinModal = false;
        this.pinValue = '';
        this.pinError = '';
    },

    async verifyPin() {
        if (this.pinValue.length < 4) return;
        
        this.isVerifyingPin = true;
        try {
            const response = await fetch('{{ route('verify-pin') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ pin: this.pinValue })
            });

            const data = await response.json();
            
            if (data.success) {
                this.showPinModal = false;
                if (this.pinCallback) this.pinCallback(data.admin_id);
                this.pinValue = '';
            } else {
                this.pinError = data.message;
                this.pinValue = '';
            }
        } catch (error) {
            this.pinError = 'Verification failed.';
            this.pinValue = '';
        } finally {
            this.isVerifyingPin = false;
        }
    }
}">
    <div class="flex h-screen bg-[--background] text-[--foreground] overflow-hidden">
        <!-- Sidebar -->
        @include('partials.sidebar')

        <div class="flex-1 min-w-0 overflow-y-auto pb-20 lg:pb-0">
            @yield('content')
        </div>

        <!-- Mobile Nav -->
        @include('partials.mobile-nav')
    </div>

    @include('partials.pin-modal')
    @include('partials.shift-modals')

    <!-- Toast Notifications -->
    <div class="fixed bottom-24 right-4 lg:bottom-8 lg:right-8 z-50 flex flex-col gap-3 pointer-events-none">
        <!-- Dynamic Notifications -->
        <template x-for="n in notifications" :key="n.id">
            <div class="px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 pointer-events-auto transition-all"
                 :class="n.type === 'error' ? 'bg-rose-600 text-white' : 'bg-emerald-600 text-white'"
                 x-transition:enter="translate-x-full" x-transition:enter-end="translate-x-0"
                 x-transition:leave="opacity-0 scale-95">
                <i :data-lucide="n.type === 'error' ? 'alert-circle' : 'check-circle'" class="w-6 h-6" x-init="$nextTick(() => lucide.createIcons($el.parentElement))"></i>
                <div class="font-bold" x-text="n.message"></div>
            </div>
        </template>

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 8000)" 
                 class="bg-emerald-600 text-white px-6 py-4 rounded-2xl shadow-2xl flex flex-col gap-3 pointer-events-auto transition-all"
                 x-transition:enter="translate-x-full" x-transition:enter-end="translate-x-0"
                 x-transition:leave="translate-x-full">
                <div class="flex items-center gap-3">
                    <i data-lucide="check-circle" class="w-6 h-6"></i>
                    <div class="font-bold">{{ session('success') }}</div>
                </div>
                @if(session('print_receipt_order_id'))
                <button onclick="window.open('{{ route('orders.receipt', session('print_receipt_order_id')) }}', 'Receipt', 'width=400,height=600')" 
                        class="mt-2 bg-white text-emerald-700 px-4 py-2 rounded-lg font-bold flex items-center justify-center gap-2 hover:bg-emerald-50 transition-colors shadow-sm">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    Print Receipt
                </button>
                @endif
            </div>
        @endif
        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                 class="bg-rose-600 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 pointer-events-auto transition-all"
                 x-transition:enter="translate-x-full" x-transition:enter-end="translate-x-0"
                 x-transition:leave="translate-x-full">
                <i data-lucide="alert-circle" class="w-6 h-6"></i>
                <div class="font-bold">{{ session('error') }}</div>
            </div>
        @endif
    </div>

    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
