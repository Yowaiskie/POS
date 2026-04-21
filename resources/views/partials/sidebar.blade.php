<aside class="hidden lg:flex flex-col w-72 bg-white border-r border-slate-200 h-screen transition-all duration-300 shadow-sm">
    <div class="p-6 border-b border-slate-200 bg-gradient-to-b from-white to-slate-50">
        <div>
            <div class="text-2xl tracking-tight font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">BOSSTON</div>
            <div class="text-xs text-slate-500 mt-1 font-medium uppercase tracking-wider">KTV Management</div>
        </div>
    </div>

    <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto">
        @php
            $navItems = [
                ['route' => 'dashboard', 'icon' => 'layout-dashboard', 'label' => 'Dashboard'],
                ['route' => 'rooms.index', 'icon' => 'door-open', 'label' => 'Manage Rooms'],
                ['route' => 'orders.index', 'icon' => 'shopping-bag', 'label' => 'Short Orders'],
                ['route' => 'menu.index', 'icon' => 'menu', 'label' => 'Menu'],
                ['route' => 'reports.index', 'icon' => 'file-text', 'label' => 'Reports'],
                ['route' => 'profile.index', 'icon' => 'user', 'label' => 'Profile'],
            ];
        @endphp

        @foreach($navItems as $item)
            <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 font-medium {{ request()->routeIs($item['route']) ? 'bg-gradient-to-r from-indigo-600 to-indigo-500 text-white shadow-lg scale-105' : 'text-slate-700 hover:bg-slate-100 active:scale-95' }}">
                <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5 flex-shrink-0"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="p-4 border-t border-slate-200 bg-slate-50">
        <div class="flex items-center gap-3 p-3 bg-white rounded-lg shadow-sm border border-slate-100">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                AU
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-xs text-slate-500 font-medium">Current Shift</div>
                <div class="text-sm font-semibold text-slate-900 truncate">Admin User</div>
            </div>
        </div>
    </div>
</aside>
