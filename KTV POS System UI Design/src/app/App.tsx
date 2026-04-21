import { useState, useEffect } from 'react';
import { Sidebar, PageType } from './components/Sidebar';
import { MobileNav } from './components/MobileNav';
import { Dashboard } from './components/Dashboard';
import { ManageRooms } from './components/ManageRooms';
import { ShortOrders } from './components/ShortOrders';
import { MenuPage } from './components/MenuPage';
import { ReportsPage } from './components/ReportsPage';
import { ProfilePage } from './components/ProfilePage';
import { NotificationSystem, Notification } from './components/NotificationSystem';
import { Room } from './components/RoomCard';

export default function App() {
  const [currentPage, setCurrentPage] = useState<PageType>('dashboard');
  const [sidebarCollapsed, setSidebarCollapsed] = useState(false);
  const [rooms, setRooms] = useState<Room[]>([
    {
      id: '1',
      name: 'Room 1',
      startTime: new Date(Date.now() - 1000 * 60 * 60),
      endTime: new Date(Date.now() + 1000 * 60 * 45),
      status: 'active',
      currentOrder: 194,
      orderItems: [
        { id: '1', name: 'Cheesy Katsu', price: 89, quantity: 1 },
        { id: '10', name: 'Hungarian', price: 105, quantity: 1 },
      ],
    },
    {
      id: '2',
      name: 'Room 2',
      startTime: new Date(Date.now() - 1000 * 60 * 90),
      endTime: new Date(Date.now() + 1000 * 60 * 8),
      status: 'warning',
      currentOrder: 294,
      orderItems: [
        { id: '13', name: 'Beef Tapa', price: 100, quantity: 1 },
        { id: '4', name: 'Creamy Salted Egg Chicken', price: 105, quantity: 1 },
        { id: '7', name: 'Sisig Popcorn Chicken', price: 89, quantity: 1 },
      ],
    },
    {
      id: '3',
      name: 'Room 3',
      startTime: new Date(Date.now() - 1000 * 60 * 120),
      endTime: new Date(Date.now() - 1000 * 60 * 5),
      status: 'overtime',
      currentOrder: 400,
      orderItems: [
        { id: '26', name: 'Hungarian + Tapa', price: 150, quantity: 1 },
        { id: '22', name: 'Beef Pepper Mushroom', price: 125, quantity: 1 },
        { id: '22', name: 'Beef Pepper Mushroom', price: 125, quantity: 1 },
      ],
    },
    {
      id: '4',
      name: 'Room 4',
      startTime: new Date(),
      endTime: new Date(),
      status: 'available',
      currentOrder: 0,
      orderItems: [],
    },
    {
      id: '5',
      name: 'Room 5',
      startTime: new Date(Date.now() - 1000 * 60 * 30),
      endTime: new Date(Date.now() + 1000 * 60 * 90),
      status: 'active',
      currentOrder: 180,
      orderItems: [
        { id: '14', name: 'Tocilog', price: 89, quantity: 2 },
      ],
    },
    {
      id: '6',
      name: 'Room 6',
      startTime: new Date(),
      endTime: new Date(),
      status: 'available',
      currentOrder: 0,
      orderItems: [],
    },
  ]);

  const [notifications, setNotifications] = useState<Notification[]>([]);

  useEffect(() => {
    const interval = setInterval(() => {
      setRooms(prevRooms => {
        const updatedRooms = prevRooms.map(room => {
          if (room.status === 'available') return room;

          const timeLeft = room.endTime.getTime() - Date.now();
          let newStatus: Room['status'] = room.status;

          if (timeLeft <= 0) {
            newStatus = 'overtime';
            if (room.status !== 'overtime') {
              addNotification(`${room.name} is now overtime`, 'danger');
            }
          } else if (timeLeft <= 10 * 60 * 1000) {
            newStatus = 'warning';
            if (room.status === 'active') {
              addNotification(`${room.name} has ${Math.floor(timeLeft / (1000 * 60))} minutes left`, 'warning');
            }
          } else {
            newStatus = 'active';
          }

          return { ...room, status: newStatus };
        });
        return updatedRooms;
      });
    }, 5000);

    return () => clearInterval(interval);
  }, []);

  const addNotification = (message: string, type: Notification['type']) => {
    const newNotif: Notification = {
      id: Date.now().toString(),
      message,
      type,
      timestamp: new Date(),
    };
    setNotifications(prev => [...prev, newNotif]);
  };

  const handleExtendTime = (roomId: string, minutes: number) => {
    setRooms(prevRooms =>
      prevRooms.map(room =>
        room.id === roomId
          ? {
              ...room,
              endTime: new Date(room.endTime.getTime() + minutes * 60 * 1000),
              status: 'active',
            }
          : room
      )
    );
    addNotification(`Extended ${rooms.find(r => r.id === roomId)?.name} by ${minutes} minutes`, 'info');
  };

  const handleEndSession = (roomId: string) => {
    const room = rooms.find(r => r.id === roomId);
    setRooms(prevRooms =>
      prevRooms.map(r =>
        r.id === roomId
          ? {
              ...r,
              status: 'available',
              currentOrder: 0,
              startTime: new Date(),
              endTime: new Date(),
              orderItems: [],
            }
          : r
      )
    );
    addNotification(`${room?.name} billed out. Total: ₱${room?.currentOrder}`, 'info');
  };

  const handleAddOrder = (roomId: string, items: any[], total: number) => {
    setRooms(prevRooms =>
      prevRooms.map(room => {
        if (room.id === roomId) {
          const newOrderItems = [...room.orderItems];

          items.forEach(newItem => {
            const existingItemIndex = newOrderItems.findIndex(item => item.id === newItem.id);
            if (existingItemIndex >= 0) {
              newOrderItems[existingItemIndex].quantity += 1;
            } else {
              newOrderItems.push({
                id: newItem.id,
                name: newItem.name,
                price: newItem.price,
                quantity: 1,
              });
            }
          });

          return {
            ...room,
            currentOrder: room.currentOrder + total,
            orderItems: newOrderItems,
          };
        }
        return room;
      })
    );
    addNotification(`Order added to ${rooms.find(r => r.id === roomId)?.name}: ₱${total}`, 'info');
  };

  const renderPage = () => {
    switch (currentPage) {
      case 'dashboard':
        return <Dashboard rooms={rooms} onNavigateToRooms={() => setCurrentPage('rooms')} />;
      case 'rooms':
        return (
          <ManageRooms
            rooms={rooms}
            onExtendTime={handleExtendTime}
            onEndSession={handleEndSession}
            onAddOrder={handleAddOrder}
          />
        );
      case 'short-orders':
        return <ShortOrders />;
      case 'menu':
        return <MenuPage />;
      case 'reports':
        return <ReportsPage />;
      case 'profile':
        return <ProfilePage />;
      default:
        return null;
    }
  };

  return (
    <div className="flex h-screen bg-[--background] text-[--foreground] overflow-hidden">
      <Sidebar
        currentPage={currentPage}
        onNavigate={setCurrentPage}
        isCollapsed={sidebarCollapsed}
        onToggleCollapse={() => setSidebarCollapsed(!sidebarCollapsed)}
      />

      <div className="flex-1 overflow-y-auto pb-20 lg:pb-0">
        {renderPage()}
      </div>

      <MobileNav currentPage={currentPage} onNavigate={setCurrentPage} />

      {notifications.length > 0 && (
        <NotificationSystem
          notifications={notifications}
          onDismiss={(id) => setNotifications(prev => prev.filter(n => n.id !== id))}
        />
      )}
    </div>
  );
}