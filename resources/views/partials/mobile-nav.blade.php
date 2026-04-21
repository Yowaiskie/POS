<nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 z-40 safe-area-inset-bottom">
    <div class="grid grid-cols-4 gap-1 p-2">
        @php
            $mobileNav = [
                ['route' => 'dashboard', 'icon' => 'layout-dashboard', 'label' => 'Dash'],
                ['route' => 'rooms.index', 'icon' => 'door-open', 'label' => 'Rooms'],
                ['route' => 'orders.index', 'icon' => 'shopping-bag', 'label' => 'Orders'],
                ['route' => 'reports.index', 'icon' => 'file-text', 'label' => 'Reports'],
            ];
        @endphp

        @foreach($mobileNav as $item)
            <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}" 
               class="flex flex-col items-center gap-1 p-3 rounded-lg transition-all active:scale-95 font-medium {{ request()->routeIs($item['route']) ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600' }}">
                <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5"></i>
                <span class="text-[10px]">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>
