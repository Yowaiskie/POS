import {
  PhilippinePeso,
  Users,
  CheckCircle,
} from "lucide-react";
import { Room } from "./RoomCard";
import { Timer } from "./Timer";

interface DashboardProps {
  rooms: Room[];
  onNavigateToRooms: () => void;
}

export function Dashboard({
  rooms,
  onNavigateToRooms,
}: DashboardProps) {
  const activeRooms = rooms.filter(
    (r) => r.status !== "available",
  );
  const availableRooms = rooms.filter(
    (r) => r.status === "available",
  );
  const totalSalesToday = rooms.reduce(
    (sum, r) => sum + r.currentOrder,
    0,
  );

  const stats = [
    {
      label: "Active Rooms",
      value: activeRooms.length,
      icon: Users,
      color: "var(--status-active)",
    },
    {
      label: "Available Rooms",
      value: availableRooms.length,
      icon: CheckCircle,
      color: "var(--neon-blue)",
    },
    {
      label: "Total Sales Today",
      value: `₱${totalSalesToday.toLocaleString()}`,
      icon: PhilippinePeso,
      color: "var(--neon-pink)",
    },
  ];

  const activeRoomsList = rooms.filter(
    (r) => r.status !== "available",
  );

  return (
    <div className="p-4 md:p-8 max-w-[1600px] mx-auto">
      <div className="mb-8">
        <h1 className="text-3xl md:text-4xl font-bold text-slate-900 mb-2">
          Dashboard Overview
        </h1>
        <p className="text-slate-600">
          Real-time monitoring and analytics for your KTV
          operations
        </p>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-8 md:mb-10">
        {stats.map((stat, i) => {
          const Icon = stat.icon;
          const gradients = [
            "from-emerald-500 to-teal-500",
            "from-blue-500 to-cyan-500",
            "from-rose-500 to-pink-500",
          ];
          return (
            <div
              key={i}
              className="bg-white border border-slate-200 rounded-xl p-6 hover:shadow-xl transition-all duration-200 hover:-translate-y-1"
              style={{ boxShadow: "var(--shadow-md)" }}
            >
              <div className="flex items-start justify-between mb-4">
                <div className="text-sm font-medium text-slate-600">
                  {stat.label}
                </div>
                <div
                  className={`w-12 h-12 rounded-xl bg-gradient-to-br ${gradients[i]} flex items-center justify-center shadow-lg`}
                >
                  <Icon className="w-6 h-6 text-white" />
                </div>
              </div>
              <div
                className="text-4xl md:text-5xl font-bold"
                style={{ color: stat.color }}
              >
                {stat.value}
              </div>
            </div>
          );
        })}
      </div>

      <div className="mb-6 md:mb-8">
        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
          <h2 className="text-xl md:text-2xl">
            Active Sessions
          </h2>
          <button
            onClick={onNavigateToRooms}
            className="px-6 py-2.5 bg-[#6366f1] text-white rounded-lg hover:bg-[#5558e3] active:scale-95 transition-all shadow-md w-full sm:w-auto font-medium"
          >
            View All Rooms
          </button>
        </div>

        {activeRoomsList.length === 0 ? (
          <div className="bg-[--card] border border-[--border] rounded-xl p-8 text-center text-[--muted-foreground] shadow-sm">
            No active sessions
          </div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            {activeRoomsList.map((room) => {
              const statusColors = {
                active:
                  "border-l-[--status-active] bg-green-50",
                warning:
                  "border-l-[--status-warning] bg-amber-50",
                overtime:
                  "border-l-[--status-danger] bg-red-50",
                available: "border-l-[--muted] bg-white",
              };

              return (
                <div
                  key={room.id}
                  className={`bg-white border border-[--border] border-l-4 ${statusColors[room.status]} rounded-lg p-4 hover:shadow-md transition-all cursor-pointer active:scale-95`}
                  onClick={onNavigateToRooms}
                >
                  <div className="flex items-start justify-between mb-3">
                    <h3 className="text-lg font-semibold">
                      {room.name}
                    </h3>
                    <div className="text-xs text-[--muted-foreground] capitalize px-2 py-1 bg-white rounded">
                      {room.status}
                    </div>
                  </div>
                  <div className="flex items-center justify-center py-2">
                    <Timer endTime={room.endTime} size="sm" />
                  </div>
                  <div className="text-right mt-3 font-semibold text-[--neon-pink]">
                    ₱{room.currentOrder.toLocaleString()}
                  </div>
                </div>
              );
            })}
          </div>
        )}
      </div>

      <div>
        <h2 className="text-xl md:text-2xl mb-4">
          Recent Activity
        </h2>
        <div className="bg-[--card] border border-[--border] rounded-xl overflow-hidden shadow-sm">
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead className="bg-[--muted] border-b border-[--border]">
                <tr>
                  <th className="text-left px-4 md:px-6 py-3 text-sm text-[--muted-foreground]">
                    Time
                  </th>
                  <th className="text-left px-4 md:px-6 py-3 text-sm text-[--muted-foreground]">
                    Room
                  </th>
                  <th className="text-left px-4 md:px-6 py-3 text-sm text-[--muted-foreground]">
                    Action
                  </th>
                  <th className="text-right px-4 md:px-6 py-3 text-sm text-[--muted-foreground]">
                    Amount
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr className="border-b border-[--border] hover:bg-[--muted]/50 transition-colors">
                  <td className="px-4 md:px-6 py-4 text-sm">
                    14:23
                  </td>
                  <td className="px-4 md:px-6 py-4">Room 2</td>
                  <td className="px-4 md:px-6 py-4 text-sm">
                    Order added
                  </td>
                  <td className="px-4 md:px-6 py-4 text-right text-[--neon-green] font-semibold">
                    +₱35
                  </td>
                </tr>
                <tr className="border-b border-[--border] hover:bg-[--muted]/50 transition-colors">
                  <td className="px-4 md:px-6 py-4 text-sm">
                    14:15
                  </td>
                  <td className="px-4 md:px-6 py-4">Room 1</td>
                  <td className="px-4 md:px-6 py-4 text-sm">
                    Session extended
                  </td>
                  <td className="px-4 md:px-6 py-4 text-right text-[--neon-blue] font-semibold">
                    +30 min
                  </td>
                </tr>
                <tr className="border-b border-[--border] hover:bg-[--muted]/50 transition-colors">
                  <td className="px-4 md:px-6 py-4 text-sm">
                    13:58
                  </td>
                  <td className="px-4 md:px-6 py-4">Room 5</td>
                  <td className="px-4 md:px-6 py-4 text-sm">
                    New session started
                  </td>
                  <td className="px-4 md:px-6 py-4 text-right text-[--neon-pink] font-semibold">
                    ₱150
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  );
}