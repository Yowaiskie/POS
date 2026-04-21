import { Timer } from './Timer';
import { Room } from './RoomCard';
import { X, Plus, ShoppingBag } from 'lucide-react';

interface RoomDetailPanelProps {
  room: Room;
  onClose: () => void;
  onExtend: (duration: number) => void;
  onAddOrder: () => void;
  onEndSession: () => void;
}

export function RoomDetailPanel({ room, onClose, onExtend, onAddOrder, onEndSession }: RoomDetailPanelProps) {
  return (
    <div
      className="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
      onClick={onClose}>
      <div
        className="bg-white border-2 border-gray-200 rounded-2xl p-6 md:p-8 max-w-3xl w-full relative shadow-2xl max-h-[90vh] overflow-y-auto"
        onClick={(e) => e.stopPropagation()}>
        <button
          onClick={onClose}
          className="absolute top-4 right-4 p-2 hover:bg-gray-100 rounded-lg transition-colors active:scale-95 z-10">
          <X className="w-6 h-6" />
        </button>

        <h2 className="text-2xl md:text-3xl mb-6 md:mb-8 font-bold">{room.name}</h2>

        <div className="flex justify-center mb-6 md:mb-8 bg-gray-50 rounded-xl py-6">
          <Timer endTime={room.endTime} size="lg" />
        </div>

        <div className="grid grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8 p-4 md:p-6 bg-gray-50 rounded-xl border border-gray-200">
          <div>
            <div className="text-xs md:text-sm text-gray-500 mb-1">Start Time</div>
            <div className="text-base md:text-lg font-semibold text-gray-900">{room.startTime.toLocaleTimeString()}</div>
          </div>
          <div>
            <div className="text-xs md:text-sm text-gray-500 mb-1">End Time</div>
            <div className="text-base md:text-lg font-semibold text-gray-900">{room.endTime.toLocaleTimeString()}</div>
          </div>
          <div>
            <div className="text-xs md:text-sm text-gray-500 mb-1">Duration</div>
            <div className="text-base md:text-lg font-semibold text-gray-900">
              {Math.floor((room.endTime.getTime() - room.startTime.getTime()) / (1000 * 60 * 60))} hours
            </div>
          </div>
          <div>
            <div className="text-xs md:text-sm text-gray-500 mb-1">Current Bill</div>
            <div className="text-base md:text-lg font-semibold text-[#ec4899]">₱{room.currentOrder.toLocaleString()}</div>
          </div>
        </div>

        {room.orderItems.length > 0 && (
          <div className="mb-6 md:mb-8">
            <div className="flex items-center gap-2 mb-4">
              <ShoppingBag className="w-5 h-5 text-[#6366f1]" />
              <h3 className="text-lg md:text-xl font-bold">Order Breakdown</h3>
            </div>
            <div className="bg-white border-2 border-gray-200 rounded-xl overflow-hidden">
              <div className="max-h-64 overflow-y-auto">
                <table className="w-full">
                  <thead className="bg-gray-50 border-b-2 border-gray-200 sticky top-0">
                    <tr>
                      <th className="text-left px-4 py-3 text-sm font-semibold text-gray-700">Item</th>
                      <th className="text-center px-4 py-3 text-sm font-semibold text-gray-700">Qty</th>
                      <th className="text-right px-4 py-3 text-sm font-semibold text-gray-700">Price</th>
                      <th className="text-right px-4 py-3 text-sm font-semibold text-gray-700">Total</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-200">
                    {room.orderItems.map((item, index) => (
                      <tr key={index} className="hover:bg-gray-50">
                        <td className="px-4 py-3 text-sm font-medium text-gray-900">{item.name}</td>
                        <td className="px-4 py-3 text-sm text-center text-gray-600">{item.quantity}</td>
                        <td className="px-4 py-3 text-sm text-right text-gray-600">₱{item.price.toLocaleString()}</td>
                        <td className="px-4 py-3 text-sm text-right font-semibold text-[#6366f1]">
                          ₱{(item.price * item.quantity).toLocaleString()}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                  <tfoot className="bg-gray-50 border-t-2 border-gray-200">
                    <tr>
                      <td colSpan={3} className="px-4 py-3 text-right font-bold text-gray-900">Total:</td>
                      <td className="px-4 py-3 text-right font-bold text-xl text-[#ec4899]">
                        ₱{room.currentOrder.toLocaleString()}
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        )}

        <div className="space-y-3">
          <div className="grid grid-cols-2 gap-3">
            <button
              onClick={() => onExtend(30)}
              className="px-4 md:px-6 py-3 bg-[#3b82f6] text-white rounded-lg hover:bg-[#2563eb] active:scale-95 transition-all shadow-md flex items-center justify-center gap-2 font-medium">
              <Plus className="w-4 h-4 md:w-5 md:h-5" />
              <span className="text-sm md:text-base">+30 mins</span>
            </button>
            <button
              onClick={() => onExtend(60)}
              className="px-4 md:px-6 py-3 bg-[#3b82f6] text-white rounded-lg hover:bg-[#2563eb] active:scale-95 transition-all shadow-md flex items-center justify-center gap-2 font-medium">
              <Plus className="w-4 h-4 md:w-5 md:h-5" />
              <span className="text-sm md:text-base">+1 hour</span>
            </button>
          </div>
          <button
            onClick={onAddOrder}
            className="w-full px-6 py-3.5 bg-[#6366f1] text-white rounded-lg hover:bg-[#5558e3] active:scale-95 transition-all shadow-md font-medium">
            Add Order
          </button>
          <button
            onClick={onEndSession}
            className="w-full px-6 py-3.5 bg-[#ec4899] text-white rounded-lg hover:bg-[#db2777] active:scale-95 transition-all shadow-md font-medium">
            Bill Out
          </button>
        </div>
      </div>
    </div>
  );
}
