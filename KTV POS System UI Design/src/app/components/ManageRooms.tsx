import { RoomCard, Room } from './RoomCard';
import { RoomDetailPanel } from './RoomDetailPanel';
import { MenuOrderingPanel } from './MenuOrderingPanel';
import { EndSessionModal } from './EndSessionModal';
import { useState } from 'react';

interface ManageRoomsProps {
  rooms: Room[];
  onExtendTime: (roomId: string, minutes: number) => void;
  onEndSession: (roomId: string) => void;
  onAddOrder: (roomId: string, items: any[], total: number) => void;
}

export function ManageRooms({ rooms, onExtendTime, onEndSession, onAddOrder }: ManageRoomsProps) {
  const [selectedRoom, setSelectedRoom] = useState<string | null>(null);
  const [orderingRoom, setOrderingRoom] = useState<string | null>(null);
  const [endingSessionRoom, setEndingSessionRoom] = useState<string | null>(null);

  const selectedRoomData = rooms.find(r => r.id === selectedRoom);
  const endingSessionRoomData = rooms.find(r => r.id === endingSessionRoom);

  const activeRooms = rooms.filter(r => r.status !== 'available');
  const availableRooms = rooms.filter(r => r.status === 'available');

  return (
    <div className="p-4 md:p-8 max-w-[1600px] mx-auto">
      <div className="mb-8">
        <h1 className="text-3xl md:text-4xl font-bold text-slate-900 mb-2">Manage Rooms</h1>
        <p className="text-slate-600">Monitor and control all KTV room sessions</p>
      </div>

      {activeRooms.length > 0 && (
        <div className="mb-10">
          <div className="flex items-center gap-3 mb-5">
            <div className="w-1 h-6 bg-gradient-to-b from-emerald-500 to-teal-500 rounded-full"></div>
            <h2 className="text-xl md:text-2xl font-bold text-slate-900">Active Sessions</h2>
            <span className="px-3 py-1 bg-emerald-100 text-emerald-700 text-sm font-semibold rounded-full">
              {activeRooms.length}
            </span>
          </div>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            {activeRooms.map(room => (
              <RoomCard
                key={room.id}
                room={room}
                onView={setSelectedRoom}
                onExtend={(id) => onExtendTime(id, 30)}
                onEnd={onEndSession}
              />
            ))}
          </div>
        </div>
      )}

      {availableRooms.length > 0 && (
        <div>
          <div className="flex items-center gap-3 mb-5">
            <div className="w-1 h-6 bg-gradient-to-b from-slate-400 to-slate-500 rounded-full"></div>
            <h2 className="text-xl md:text-2xl font-bold text-slate-900">Available Rooms</h2>
            <span className="px-3 py-1 bg-slate-100 text-slate-600 text-sm font-semibold rounded-full">
              {availableRooms.length}
            </span>
          </div>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            {availableRooms.map(room => (
              <RoomCard
                key={room.id}
                room={room}
                onView={setSelectedRoom}
                onExtend={(id) => onExtendTime(id, 30)}
                onEnd={onEndSession}
              />
            ))}
          </div>
        </div>
      )}

      {selectedRoomData && (
        <RoomDetailPanel
          room={selectedRoomData}
          onClose={() => setSelectedRoom(null)}
          onExtend={(minutes) => onExtendTime(selectedRoomData.id, minutes)}
          onAddOrder={() => {
            setOrderingRoom(selectedRoomData.id);
            setSelectedRoom(null);
          }}
          onEndSession={() => {
            setEndingSessionRoom(selectedRoomData.id);
            setSelectedRoom(null);
          }}
        />
      )}

      {orderingRoom && (
        <MenuOrderingPanel
          roomId={orderingRoom}
          onClose={() => setOrderingRoom(null)}
          onSubmitOrder={(items, total) => {
            onAddOrder(orderingRoom, items, total);
          }}
          onBack={() => {
            setOrderingRoom(null);
            setSelectedRoom(orderingRoom);
          }}
        />
      )}

      {endingSessionRoomData && (
        <EndSessionModal
          roomName={endingSessionRoomData.name}
          totalAmount={endingSessionRoomData.currentOrder}
          startTime={endingSessionRoomData.startTime}
          endTime={new Date()}
          orderItems={endingSessionRoomData.orderItems}
          onConfirm={() => {
            onEndSession(endingSessionRoomData.id);
            setEndingSessionRoom(null);
          }}
          onCancel={() => setEndingSessionRoom(null)}
        />
      )}
    </div>
  );
}
