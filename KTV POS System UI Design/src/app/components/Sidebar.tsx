import { LayoutDashboard, DoorOpen, ShoppingBag, Menu as MenuIcon, FileText, User, ChevronLeft, ChevronRight } from 'lucide-react';

export type PageType = 'dashboard' | 'rooms' | 'short-orders' | 'menu' | 'reports' | 'profile';

interface SidebarProps {
  currentPage: PageType;
  onNavigate: (page: PageType) => void;
  isCollapsed: boolean;
  onToggleCollapse: () => void;
}

export function Sidebar({ currentPage, onNavigate, isCollapsed, onToggleCollapse }: SidebarProps) {
  const navItems = [
    { id: 'dashboard' as PageType, icon: LayoutDashboard, label: 'Dashboard' },
    { id: 'rooms' as PageType, icon: DoorOpen, label: 'Manage Rooms' },
    { id: 'short-orders' as PageType, icon: ShoppingBag, label: 'Short Orders' },
    { id: 'menu' as PageType, icon: MenuIcon, label: 'Menu' },
    { id: 'reports' as PageType, icon: FileText, label: 'Reports' },
    { id: 'profile' as PageType, icon: User, label: 'Profile' },
  ];

  return (
    <div className={`h-screen bg-[--sidebar] border-r border-[--border] flex flex-col hidden lg:flex transition-all duration-300 ${isCollapsed ? 'w-20' : 'w-72'}`} style={{ boxShadow: 'var(--shadow-sm)' }}>
      <div className="p-6 border-b border-[--border] flex items-center justify-between bg-gradient-to-b from-white to-gray-50">
        {!isCollapsed && (
          <div>
            <div className="text-2xl tracking-tight font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">BOSSTON</div>
            <div className="text-xs text-[--muted-foreground] mt-1 font-medium">KTV Management System</div>
          </div>
        )}
        <button
          onClick={onToggleCollapse}
          className="p-2 hover:bg-gray-100 rounded-lg transition-all active:scale-95"
          style={{ boxShadow: 'var(--shadow-sm)' }}>
          {isCollapsed ? <ChevronRight className="w-5 h-5" /> : <ChevronLeft className="w-5 h-5" />}
        </button>
      </div>

      <nav className="flex-1 p-4 space-y-1.5 overflow-y-auto">
        {navItems.map(item => {
          const Icon = item.icon;
          const isActive = currentPage === item.id;

          return (
            <button
              key={item.id}
              onClick={() => onNavigate(item.id)}
              title={isCollapsed ? item.label : ''}
              className={`w-full flex items-center ${isCollapsed ? 'justify-center' : 'gap-3'} px-4 py-3 rounded-lg transition-all duration-200 font-medium ${
                isActive
                  ? 'bg-gradient-to-r from-indigo-600 to-indigo-500 text-white shadow-lg scale-105'
                  : 'text-slate-700 hover:bg-slate-100 active:scale-95'
              }`}>
              <Icon className="w-5 h-5 flex-shrink-0" />
              {!isCollapsed && <span className={isActive ? 'text-white' : ''}>{item.label}</span>}
            </button>
          );
        })}
      </nav>

      {!isCollapsed && (
        <div className="p-4 border-t border-[--border] bg-gray-50">
          <div className="flex items-center gap-3 p-3 bg-white rounded-lg" style={{ boxShadow: 'var(--shadow-sm)' }}>
            <div className="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
              AU
            </div>
            <div className="flex-1 min-w-0">
              <div className="text-xs text-[--muted-foreground] font-medium">Current Shift</div>
              <div className="text-sm font-semibold text-slate-900 truncate">Admin User</div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
