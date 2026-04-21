import { Timer } from './Timer';

export interface OrderItem {
  id: string;
  name: string;
  price: number;
  quantity: number;
}

export interface Room {
  id: string;
  name: string;
  startTime: Date;
  endTime: Date;
  status: 'active' | 'warning' | 'overtime' | 'available';
  currentOrder: number;
  orderItems: OrderItem[];
}

interface RoomCardProps {
  room: Room;
  onView: (roomId: string) => void;
  onExtend: (roomId: string) => void;
  onEnd: (roomId: string) => void;
}

export function RoomCard({ room, onView, onExtend, onEnd }: RoomCardProps) {
  const statusConfig = {
    active: {
      bg: 'bg-emerald-50',
      border: 'border-emerald-200',
      dot: 'bg-emerald-500',
      badge: 'bg-emerald-100 text-emerald-700 border-emerald-200'
    },
    warning: {
      bg: 'bg-amber-50',
      border: 'border-amber-200',
      dot: 'bg-amber-500',
      badge: 'bg-amber-100 text-amber-700 border-amber-200'
    },
    overtime: {
      bg: 'bg-red-50',
      border: 'border-red-200',
      dot: 'bg-red-500',
      badge: 'bg-red-100 text-red-700 border-red-200'
    },
    available: {
      bg: 'bg-white',
      border: 'border-slate-200',
      dot: 'bg-slate-400',
      badge: 'bg-slate-100 text-slate-600 border-slate-200'
    }
  };

  const config = statusConfig[room.status];

  return (
    <div
      className={`rounded-xl border ${config.border} ${config.bg} p-6 transition-all duration-200 hover:shadow-xl hover:-translate-y-1 h-full flex flex-col`}
      style={{ boxShadow: 'var(--shadow)' }}>
      <div className="flex items-start justify-between mb-4">
        <div className="flex-1">
          <h3 className="text-xl font-bold text-slate-900 mb-3">{room.name}</h3>
          <span className={`inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold border ${config.badge}`}>
            <span className={`w-2 h-2 rounded-full ${config.dot} ${room.status !== 'available' ? 'animate-pulse' : ''}`}></span>
            {room.status.charAt(0).toUpperCase() + room.status.slice(1)}
          </span>
        </div>
        {room.currentOrder > 0 && (
          <div className="text-right bg-white px-4 py-2 rounded-lg border border-slate-200" style={{ boxShadow: 'var(--shadow-sm)' }}>
            <div className="text-xs text-slate-500 font-medium">Current Bill</div>
            <div className="text-xl font-bold text-indigo-600">₱{room.currentOrder.toLocaleString()}</div>
          </div>
        )}
      </div>

      {room.status !== 'available' && (
        <div className="my-6 flex justify-center flex-1 items-center bg-white rounded-lg py-4" style={{ boxShadow: 'var(--shadow-sm)' }}>
          <Timer endTime={room.endTime} size="sm" />
        </div>
      )}

      <div className="grid grid-cols-1 gap-2.5 mt-auto">
        <button
          onClick={() => onView(room.id)}
          className="w-full px-4 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white rounded-lg hover:from-indigo-700 hover:to-indigo-600 active:scale-98 transition-all font-semibold shadow-md hover:shadow-lg">
          View Details
        </button>
        {room.status !== 'available' && (
          <div className="grid grid-cols-2 gap-2.5">
            <button
              onClick={() => onExtend(room.id)}
              className="px-4 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 active:scale-98 transition-all font-semibold shadow-sm">
              Extend
            </button>
            <button
              onClick={() => onEnd(room.id)}
              className="px-4 py-2.5 bg-rose-500 text-white rounded-lg hover:bg-rose-600 active:scale-98 transition-all font-semibold shadow-sm">
              End
            </button>
          </div>
        )}
      </div>
    </div>
  );
}
