import { LayoutDashboard, DoorOpen, ShoppingBag, FileText } from 'lucide-react';
import { PageType } from './Sidebar';

interface MobileNavProps {
  currentPage: PageType;
  onNavigate: (page: PageType) => void;
}

export function MobileNav({ currentPage, onNavigate }: MobileNavProps) {
  const navItems = [
    { id: 'dashboard' as PageType, icon: LayoutDashboard, label: 'Dashboard' },
    { id: 'rooms' as PageType, icon: DoorOpen, label: 'Rooms' },
    { id: 'short-orders' as PageType, icon: ShoppingBag, label: 'Orders' },
    { id: 'reports' as PageType, icon: FileText, label: 'Reports' },
  ];

  return (
    <div className="lg:hidden fixed bottom-0 left-0 right-0 bg-[--sidebar] border-t border-[--border] z-40 safe-area-inset-bottom">
      <div className="grid grid-cols-4 gap-1 p-2">
        {navItems.map(item => {
          const Icon = item.icon;
          const isActive = currentPage === item.id;

          return (
            <button
              key={item.id}
              onClick={() => onNavigate(item.id)}
              className={`flex flex-col items-center gap-1 p-3 rounded-lg transition-all active:scale-95 font-medium ${
                isActive
                  ? 'bg-[#6366f1] text-white shadow-md'
                  : 'text-gray-600 active:bg-gray-100'
              }`}>
              <Icon className="w-5 h-5" />
              <span className="text-xs">{item.label}</span>
            </button>
          );
        })}
      </div>
    </div>
  );
}
