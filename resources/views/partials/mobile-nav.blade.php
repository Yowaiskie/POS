<div class="lg:hidden fixed bottom-0 left-0 right-0 bg-[--sidebar] border-t border-[--border] z-40 safe-area-inset-bottom">
    <div class="grid grid-cols-3 gap-1 p-2">
        @php
            $navItems = [
                ['route' => 'dashboard', 'icon' => 'layout-dashboard', 'label' => 'Dashboard'],
                ['route' => 'rooms.index', 'icon' => 'door-open', 'label' => 'Rooms'],
                ['route' => 'orders.index', 'icon' => 'shopping-bag', 'label' => 'Orders'],
            ];
            
            if (auth()->check() && strtolower(auth()->user()->position) === 'admin') {
                $navItems[] = ['route' => 'inventory.index', 'icon' => 'package',     'label' => 'Inventory'];
                $navItems[] = ['route' => 'reports.index',   'icon' => 'file-text',   'label' => 'Reports'];
                $navItems[] = ['route' => 'users.index',     'icon' => 'users',       'label' => 'Users'];
            }
        @endphp

        @foreach($navItems as $item)
            @php
                $isActive = request()->routeIs($item['route']);
            @endphp
            <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}" 
               class="flex flex-col items-center gap-1 p-3 rounded-lg transition-all active:scale-95 font-medium {{ $isActive ? 'bg-[#6366f1] text-white shadow-md' : 'text-gray-600 active:bg-gray-100' }}">
                <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5"></i>
                <span class="text-xs">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
</div>
