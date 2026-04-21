import { X, ShoppingBag } from 'lucide-react';
import { OrderItem } from './RoomCard';
import { useState } from 'react';

interface EndSessionModalProps {
  roomName: string;
  totalAmount: number;
  startTime: Date;
  endTime: Date;
  orderItems: OrderItem[];
  onConfirm: () => void;
  onCancel: () => void;
}

export function EndSessionModal({ roomName, totalAmount, startTime, endTime, orderItems, onConfirm, onCancel }: EndSessionModalProps) {
  const duration = Math.floor((endTime.getTime() - startTime.getTime()) / (1000 * 60 * 60 * 60));
  const hours = Math.floor(duration / 60);
  const minutes = duration % 60;

  const [paymentMethod, setPaymentMethod] = useState<'cash' | 'gcash' | null>(null);
  const [transactionNumber, setTransactionNumber] = useState('');
  const [amountReceived, setAmountReceived] = useState('');

  const receivedAmount = parseFloat(amountReceived) || 0;
  const change = receivedAmount - totalAmount;

  const handleConfirm = () => {
    if (paymentMethod === 'gcash') {
      if (!transactionNumber.trim()) {
        alert('Please enter transaction number');
        return;
      }
      onConfirm();
    } else if (paymentMethod === 'cash') {
      if (receivedAmount < totalAmount) {
        alert('Amount received is less than total amount');
        return;
      }
      onConfirm();
    }
  };

  return (
    <div
      className="fixed inset-0 bg-black/60 z-[60] flex items-center justify-center p-4"
      onClick={onCancel}>
      <div
        className="bg-white border-2 border-gray-200 rounded-2xl p-6 md:p-8 max-w-2xl w-full relative shadow-2xl max-h-[90vh] overflow-y-auto"
        onClick={(e) => e.stopPropagation()}>
        <button
          onClick={onCancel}
          className="absolute top-4 right-4 p-2 hover:bg-gray-100 rounded-lg transition-colors active:scale-95 z-10">
          <X className="w-6 h-6" />
        </button>

        <h2 className="text-2xl md:text-3xl mb-6 font-bold text-center">Bill Out</h2>

        <div className="bg-gray-50 border-2 border-gray-200 rounded-xl p-6 mb-6">
          <div className="text-center mb-6">
            <div className="text-lg font-semibold text-gray-600 mb-2">{roomName}</div>
            <div className="text-5xl font-bold text-[#6366f1] mb-2">₱{totalAmount.toLocaleString()}</div>
            <div className="text-sm text-gray-500">Total Amount</div>
          </div>

          <div className="border-t border-gray-300 pt-4 space-y-3">
            <div className="flex justify-between text-sm">
              <span className="text-gray-600">Start Time</span>
              <span className="font-semibold">{startTime.toLocaleString()}</span>
            </div>
            <div className="flex justify-between text-sm">
              <span className="text-gray-600">End Time</span>
              <span className="font-semibold">{endTime.toLocaleString()}</span>
            </div>
            <div className="flex justify-between text-sm">
              <span className="text-gray-600">Duration</span>
              <span className="font-semibold">{hours}h {minutes}m</span>
            </div>
          </div>
        </div>

        {orderItems.length > 0 && (
          <div className="mb-6">
            <div className="flex items-center gap-2 mb-3">
              <ShoppingBag className="w-5 h-5 text-[#6366f1]" />
              <h3 className="text-lg font-bold">Order Breakdown</h3>
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
                    {orderItems.map((item, index) => (
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
                      <td className="px-4 py-3 text-right font-bold text-lg text-[#ec4899]">
                        ₱{totalAmount.toLocaleString()}
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        )}

        {!paymentMethod ? (
          <>
            <div className="mb-6">
              <label className="block text-sm font-semibold text-gray-700 mb-3">Select Payment Method</label>
              <div className="grid grid-cols-2 gap-3">
                <button
                  onClick={() => setPaymentMethod('cash')}
                  className="px-6 py-4 bg-white border-2 border-gray-200 text-gray-700 rounded-lg hover:border-[#6366f1] hover:bg-gray-50 active:scale-95 transition-all font-medium">
                  Cash
                </button>
                <button
                  onClick={() => setPaymentMethod('gcash')}
                  className="px-6 py-4 bg-white border-2 border-gray-200 text-gray-700 rounded-lg hover:border-[#6366f1] hover:bg-gray-50 active:scale-95 transition-all font-medium">
                  G-Cash
                </button>
              </div>
            </div>

            <button
              onClick={onCancel}
              className="w-full px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 active:scale-95 transition-all font-medium">
              Cancel
            </button>
          </>
        ) : paymentMethod === 'gcash' ? (
          <>
            <div className="mb-6">
              <label className="block text-sm font-semibold text-slate-700 mb-2">
                G-Cash Transaction Number
              </label>
              <input
                type="text"
                value={transactionNumber}
                onChange={(e) => setTransactionNumber(e.target.value)}
                placeholder="Enter transaction number"
                className="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-indigo-500 focus:outline-none text-lg"
                autoFocus
              />
            </div>

            <div className="grid grid-cols-2 gap-3">
              <button
                onClick={() => {
                  setPaymentMethod(null);
                  setTransactionNumber('');
                }}
                className="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 active:scale-95 transition-all font-medium">
                Back
              </button>
              <button
                onClick={handleConfirm}
                disabled={!transactionNumber.trim()}
                className="px-6 py-3 bg-[#ec4899] text-white rounded-lg hover:bg-[#db2777] active:scale-95 transition-all shadow-md font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                Bill Out
              </button>
            </div>
          </>
        ) : (
          <>
            <div className="mb-4">
              <label className="block text-sm font-semibold text-slate-700 mb-2">
                Amount Received
              </label>
              <input
                type="number"
                value={amountReceived}
                onChange={(e) => setAmountReceived(e.target.value)}
                placeholder="Enter amount received"
                className="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-indigo-500 focus:outline-none text-lg"
                autoFocus
              />
            </div>

            {receivedAmount > 0 && (
              <div className="mb-6 p-4 bg-indigo-50 rounded-lg border border-indigo-200">
                <div className="flex justify-between items-center mb-2">
                  <span className="text-sm text-slate-600">Total Amount</span>
                  <span className="font-semibold text-slate-900">₱{totalAmount.toLocaleString()}</span>
                </div>
                <div className="flex justify-between items-center mb-2">
                  <span className="text-sm text-slate-600">Amount Received</span>
                  <span className="font-semibold text-slate-900">₱{receivedAmount.toLocaleString()}</span>
                </div>
                <div className="flex justify-between items-center pt-2 border-t border-indigo-300">
                  <span className="text-sm font-semibold text-slate-700">Change</span>
                  <span className="text-xl font-bold text-indigo-600">₱{change.toLocaleString()}</span>
                </div>
              </div>
            )}

            <div className="grid grid-cols-2 gap-3">
              <button
                onClick={() => {
                  setPaymentMethod(null);
                  setAmountReceived('');
                }}
                className="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 active:scale-95 transition-all font-medium">
                Back
              </button>
              <button
                onClick={handleConfirm}
                disabled={receivedAmount < totalAmount}
                className="px-6 py-3 bg-[#ec4899] text-white rounded-lg hover:bg-[#db2777] active:scale-95 transition-all shadow-md font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                Bill Out
              </button>
            </div>
          </>
        )}
      </div>
    </div>
  );
}
