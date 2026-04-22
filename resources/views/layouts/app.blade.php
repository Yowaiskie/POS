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
    toggleSidebar() {
        this.sidebarCollapsed = !this.sidebarCollapsed;
        localStorage.setItem('sidebarCollapsed', JSON.stringify(this.sidebarCollapsed));
    }
}" x-effect="notifications.length; $nextTick(() => lucide.createIcons())">
    <div class="flex h-screen bg-[--background] text-[--foreground] overflow-hidden">
        <!-- Sidebar -->
        @include('partials.sidebar')

        <div class="flex-1 min-w-0 overflow-y-auto pb-20 lg:pb-0">
            @yield('content')
        </div>

        <!-- Mobile Nav -->
        @include('partials.mobile-nav')
    </div>

    <!-- Toast Notifications -->
    <div class="fixed bottom-24 right-4 lg:bottom-8 lg:right-8 z-50 flex flex-col gap-3 pointer-events-none">
        <!-- Dynamic Notifications -->
        <template x-for="n in notifications" :key="n.id">
            <div class="px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 pointer-events-auto transition-all"
                 :class="n.type === 'error' ? 'bg-rose-600 text-white' : 'bg-emerald-600 text-white'"
                 x-transition:enter="translate-x-full" x-transition:enter-end="translate-x-0"
                 x-transition:leave="opacity-0 scale-95">
                <i :data-lucide="n.type === 'error' ? 'alert-circle' : 'check-circle'" class="w-6 h-6"></i>
                <div class="font-bold" x-text="n.message"></div>
            </div>
        </template>

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                 class="bg-emerald-600 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 pointer-events-auto transition-all"
                 x-transition:enter="translate-x-full" x-transition:enter-end="translate-x-0"
                 x-transition:leave="translate-x-full">
                <i data-lucide="check-circle" class="w-6 h-6"></i>
                <div class="font-bold">{{ session('success') }}</div>
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
