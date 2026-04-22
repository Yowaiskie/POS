<div class="h-screen bg-[--sidebar] border-r border-[--border] flex flex-col hidden lg:flex shrink-0 overflow-hidden transition-[width] duration-300 ease-in-out"
     style="box-shadow: var(--shadow-sm)"
     :class="sidebarCollapsed ? 'w-[72px]' : 'w-64'">

    {{-- Header --}}
    <div class="h-16 px-3 border-b border-[--border] flex items-center bg-gradient-to-b from-white to-gray-50 shrink-0"
         :class="sidebarCollapsed ? 'justify-center' : 'justify-between px-4'">
        <div x-show="!sidebarCollapsed" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="overflow-hidden">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-10 w-10 object-cover rounded-full border border-gray-200">
        </div>
        <button @click="toggleSidebar()"
                class="p-2 hover:bg-gray-100 rounded-lg transition-all active:scale-95 shrink-0"
                :title="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'">
            {{-- Use two separate icons toggled via x-show to avoid :data-lucide dynamic issue --}}
            <svg x-show="!sidebarCollapsed" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            <svg x-show="sidebarCollapsed"  xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
    </div>

    {{-- Nav Items --}}
    <nav class="flex-1 p-3 space-y-1 overflow-y-auto overflow-x-hidden">
        @php
            $navItems = [
                ['route' => 'dashboard',   'icon' => 'layout-dashboard', 'label' => 'Dashboard'],
                ['route' => 'rooms.index', 'icon' => 'door-open',        'label' => 'Manage Rooms'],
                ['route' => 'orders.index','icon' => 'shopping-bag',     'label' => 'Short Orders'],
            ];

            if (auth()->check() && strtolower(auth()->user()->position) === 'admin') {
                $navItems[] = ['route' => 'menu.index',       'icon' => 'utensils',    'label' => 'Menu'];
                $navItems[] = ['route' => 'inventory.index',  'icon' => 'package',     'label' => 'Inventory'];
                $navItems[] = ['route' => 'reports.index',    'icon' => 'bar-chart-2', 'label' => 'Reports'];
                $navItems[] = ['route' => 'users.index',      'icon' => 'users',       'label' => 'User Management'];
            }

            $navItems[] = ['route' => 'profile.index', 'icon' => 'user-circle', 'label' => 'Profile'];
        @endphp

        @foreach($navItems as $item)
            @php $isActive = request()->routeIs($item['route']); @endphp
            <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
               title="{{ $item['label'] }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 font-medium group
                      {{ $isActive
                            ? 'bg-gradient-to-r from-indigo-600 to-indigo-500 text-white shadow-md'
                            : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
               :class="sidebarCollapsed ? 'justify-center' : ''">
                <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5 shrink-0 {{ $isActive ? 'text-white' : 'text-slate-500 group-hover:text-indigo-600' }}"></i>
                <span x-show="!sidebarCollapsed"
                      x-transition:enter="transition ease-out duration-200"
                      x-transition:enter-start="opacity-0"
                      x-transition:enter-end="opacity-100"
                      class="whitespace-nowrap text-sm overflow-hidden">
                    {{ $item['label'] }}
                </span>
            </a>
        @endforeach
    </nav>

    {{-- User Info + Logout --}}
    <div class="border-t border-[--border] bg-gray-50 shrink-0 overflow-hidden transition-all duration-300"
         :class="sidebarCollapsed ? 'p-2' : 'p-3'">

        {{-- Avatar + Info (collapsed: just avatar) --}}
        <div class="flex items-center gap-3 mb-2" :class="sidebarCollapsed ? 'justify-center' : ''">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs shrink-0">
                {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'GU' }}
            </div>
            <div x-show="!sidebarCollapsed"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="flex-1 min-w-0 overflow-hidden">
                <div class="text-xs font-semibold text-slate-900 truncate">{{ auth()->check() ? auth()->user()->name : 'Guest User' }}</div>
                <div class="text-[10px] text-slate-400 truncate capitalize">{{ auth()->check() ? auth()->user()->position : 'Guest' }}</div>
            </div>
        </div>

        {{-- Logout Button --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    title="Logout"
                    class="w-full flex items-center gap-2 px-3 py-2 text-xs text-red-600 font-semibold hover:bg-red-50 rounded-lg transition-colors"
                    :class="sidebarCollapsed ? 'justify-center' : ''">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                <span x-show="!sidebarCollapsed"
                      x-transition:enter="transition ease-out duration-200"
                      x-transition:enter-start="opacity-0"
                      x-transition:enter-end="opacity-100"
                      class="whitespace-nowrap">Logout</span>
            </button>
        </form>
    </div>
</div>
