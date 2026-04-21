import { useState } from 'react';
import { ShoppingCart, Trash2, Plus, Minus, X } from 'lucide-react';

interface MenuItem {
  id: string;
  name: string;
  price: number;
  category: 'bowl-meal' | 'silog-meal' | 'sizzling-plate' | 'combo-meal';
}

const menuItems: MenuItem[] = [
  // Bowl Meal
  { id: '1', name: 'Cheesy Katsu', price: 89, category: 'bowl-meal' },
  { id: '2', name: 'Cheesy Karaage', price: 89, category: 'bowl-meal' },
  { id: '3', name: 'Mushroom Gravy', price: 89, category: 'bowl-meal' },
  { id: '4', name: 'Creamy Salted Egg Chicken', price: 105, category: 'bowl-meal' },
  { id: '5', name: 'Orange Chicken', price: 89, category: 'bowl-meal' },
  { id: '6', name: 'Buffalo Pops', price: 89, category: 'bowl-meal' },
  { id: '7', name: 'Sisig Popcorn Chicken', price: 89, category: 'bowl-meal' },
  { id: '8', name: 'Chicken Skin', price: 89, category: 'bowl-meal' },

  // Silog Meal
  { id: '9', name: 'Cornedbeef', price: 75, category: 'silog-meal' },
  { id: '10', name: 'Hungarian', price: 105, category: 'silog-meal' },
  { id: '11', name: 'Burgersteak', price: 99, category: 'silog-meal' },
  { id: '12', name: 'Boneless Fried Chicken', price: 115, category: 'silog-meal' },
  { id: '13', name: 'Beef Tapa', price: 100, category: 'silog-meal' },
  { id: '14', name: 'Tocilog', price: 89, category: 'silog-meal' },
  { id: '15', name: 'Bacsilog', price: 99, category: 'silog-meal' },
  { id: '16', name: 'Korean Spam', price: 89, category: 'silog-meal' },

  // Sizzling Plate
  { id: '17', name: 'Pork Sisig w/ egg', price: 115, category: 'sizzling-plate' },
  { id: '18', name: 'Crispy Bagnet', price: 110, category: 'sizzling-plate' },
  { id: '19', name: 'Fried Liempo', price: 120, category: 'sizzling-plate' },
  { id: '20', name: 'Porkchop', price: 115, category: 'sizzling-plate' },
  { id: '21', name: 'Crispy Pork Binagoongan', price: 120, category: 'sizzling-plate' },
  { id: '22', name: 'Beef Pepper Mushroom', price: 125, category: 'sizzling-plate' },

  // Combo Meal
  { id: '23', name: 'Bacon + Bagnet', price: 130, category: 'combo-meal' },
  { id: '24', name: 'Hotdog + Tapa', price: 140, category: 'combo-meal' },
  { id: '25', name: 'Bagnet + Sisig', price: 145, category: 'combo-meal' },
  { id: '26', name: 'Hungarian + Tapa', price: 150, category: 'combo-meal' },
  { id: '27', name: 'Hungarian + Sisig', price: 150, category: 'combo-meal' },
  { id: '28', name: 'Hungarian + Bagnet', price: 150, category: 'combo-meal' },
  { id: '29', name: 'Liempo + Hungarian', price: 150, category: 'combo-meal' },
  { id: '30', name: 'Liempo + Tapa', price: 150, category: 'combo-meal' },
  { id: '31', name: 'Tapa + Sisig', price: 150, category: 'combo-meal' },
];

export function ShortOrders() {
  const [selectedCategory, setSelectedCategory] = useState<MenuItem['category']>('bowl-meal');
  const [cart, setCart] = useState<Map<string, number>>(new Map());
  const [paymentMethod, setPaymentMethod] = useState<'cash' | 'gcash'>('cash');
  const [showPaymentModal, setShowPaymentModal] = useState(false);
  const [transactionNumber, setTransactionNumber] = useState('');
  const [amountReceived, setAmountReceived] = useState('');

  const categories = [
    { id: 'bowl-meal' as const, name: 'Bowl Meal', icon: '🍜' },
    { id: 'silog-meal' as const, name: 'Silog Meal', icon: '🍳' },
    { id: 'sizzling-plate' as const, name: 'Sizzling Plate', icon: '🔥' },
    { id: 'combo-meal' as const, name: 'Combo Meal', icon: '🍱' },
  ];

  const filteredItems = menuItems.filter(item => item.category === selectedCategory);

  const addToCart = (item: MenuItem) => {
    const newCart = new Map(cart);
    newCart.set(item.id, (newCart.get(item.id) || 0) + 1);
    setCart(newCart);
  };

  const updateQuantity = (itemId: string, delta: number) => {
    const newCart = new Map(cart);
    const currentQty = newCart.get(itemId) || 0;
    const newQty = Math.max(0, currentQty + delta);

    if (newQty === 0) {
      newCart.delete(itemId);
    } else {
      newCart.set(itemId, newQty);
    }
    setCart(newCart);
  };

  const setQuantity = (itemId: string, value: string) => {
    const newCart = new Map(cart);
    const qty = parseInt(value) || 0;

    if (qty <= 0) {
      newCart.delete(itemId);
    } else {
      newCart.set(itemId, qty);
    }
    setCart(newCart);
  };

  const cartItems = Array.from(cart.entries()).map(([itemId, qty]) => ({
    item: menuItems.find(i => i.id === itemId)!,
    quantity: qty,
  }));

  const cartTotal = cartItems.reduce((sum, { item, quantity }) => sum + item.price * quantity, 0);

  const receivedAmount = parseFloat(amountReceived) || 0;
  const change = receivedAmount - cartTotal;

  const handleCheckout = () => {
    if (cartItems.length === 0) return;
    setShowPaymentModal(true);
  };

  const completeCheckout = () => {
    if (paymentMethod === 'gcash' && !transactionNumber.trim()) {
      alert('Please enter transaction number');
      return;
    }
    if (paymentMethod === 'cash' && receivedAmount < cartTotal) {
      alert('Amount received is less than total amount');
      return;
    }

    const paymentDetails = paymentMethod === 'gcash'
      ? `Payment Method: G-Cash\nTransaction #: ${transactionNumber}`
      : `Payment Method: Cash\nAmount Received: ₱${receivedAmount.toLocaleString()}\nChange: ₱${change.toLocaleString()}`;

    alert(`Order placed!\nTotal: ₱${cartTotal.toLocaleString()}\n${paymentDetails}`);
    setCart(new Map());
    setPaymentMethod('cash');
    setTransactionNumber('');
    setAmountReceived('');
    setShowPaymentModal(false);
  };

  const clearCart = () => {
    setCart(new Map());
    setTransactionNumber('');
    setAmountReceived('');
  };

  return (
    <div className="flex h-screen flex-col lg:flex-row">
      <div className="w-full lg:w-48 bg-[--sidebar] border-b lg:border-b-0 lg:border-r border-[--border] p-4">
        <h2 className="text-sm text-[--muted-foreground] mb-4 px-2 hidden lg:block">Categories</h2>
        <div className="flex lg:flex-col gap-2 overflow-x-auto lg:overflow-x-visible">
          {categories.map(cat => (
            <button
              key={cat.id}
              onClick={() => setSelectedCategory(cat.id)}
              className={`flex-shrink-0 lg:w-full p-3 md:p-4 rounded-lg text-left transition-all active:scale-95 font-medium ${
                selectedCategory === cat.id
                  ? 'bg-[#6366f1] text-white shadow-md'
                  : 'bg-white border-2 border-gray-200 hover:bg-gray-50'
              }`}>
              <div className="text-2xl mb-1">{cat.icon}</div>
              <div className="text-sm md:text-base">{cat.name}</div>
            </button>
          ))}
        </div>
      </div>

      <div className="flex-1 p-4 md:p-8 overflow-y-auto bg-slate-50">
        <div className="max-w-[1600px] mx-auto">
          <div className="mb-8">
            <h1 className="text-3xl md:text-4xl font-bold text-slate-900 mb-2">Short Orders</h1>
            <p className="text-slate-600">Quick point-of-sale for walk-in customers</p>
          </div>
          <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            {filteredItems.map(item => (
              <button
                key={item.id}
                onClick={() => addToCart(item)}
                className="bg-white border border-slate-200 rounded-xl p-6 hover:border-indigo-400 hover:shadow-xl hover:-translate-y-1 transition-all duration-200 active:scale-98 flex flex-col gap-4 min-h-[150px]"
                style={{ boxShadow: 'var(--shadow)' }}>
                <h3 className="text-left font-semibold text-slate-900 leading-snug line-clamp-2 flex-1">
                  {item.name}
                </h3>
                <div className="text-left">
                  <div className="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">₱{item.price.toLocaleString()}</div>
                </div>
              </button>
            ))}
          </div>
        </div>
      </div>

      <div className="w-full lg:w-96 bg-[--sidebar] border-t lg:border-t-0 lg:border-l border-[--border] flex flex-col max-h-[50vh] lg:max-h-none">
        <div className="p-4 md:p-6 border-b border-[--border]">
          <div className="flex items-center gap-3 mb-2">
            <ShoppingCart className="w-5 h-5 md:w-6 md:h-6 text-[--neon-violet]" />
            <h2 className="text-xl md:text-2xl font-semibold">Order Summary</h2>
          </div>
          <div className="text-sm text-[--muted-foreground]">
            {cart.size} item{cart.size !== 1 ? 's' : ''}
          </div>
        </div>

        <div className="flex-1 overflow-y-auto p-4 md:p-6 space-y-3">
          {cartItems.length === 0 ? (
            <div className="text-center text-gray-400 py-12 text-lg">
              Cart is empty
            </div>
          ) : (
            cartItems.map(({ item, quantity }) => (
              <div key={item.id} className="bg-white border-2 border-gray-200 rounded-lg p-4 shadow-sm">
                <div className="flex justify-between items-start mb-3">
                  <div className="flex-1 pr-2">
                    <div className="mb-1 font-semibold text-sm md:text-base">{item.name}</div>
                    <div className="text-xs md:text-sm text-gray-500">₱{item.price.toLocaleString()} each</div>
                  </div>
                  <div className="text-base md:text-lg font-bold text-[#ec4899]">₱{(item.price * quantity).toLocaleString()}</div>
                </div>
                <div className="flex items-center gap-3">
                  <button
                    onClick={() => updateQuantity(item.id, -1)}
                    className="w-9 h-9 flex items-center justify-center bg-gray-200 hover:bg-gray-300 active:scale-95 rounded-lg transition-all font-bold">
                    <Minus className="w-4 h-4" />
                  </button>
                  <input
                    type="number"
                    min="1"
                    value={quantity}
                    onChange={(e) => setQuantity(item.id, e.target.value)}
                    className="flex-1 text-center font-bold text-lg border-2 border-gray-200 rounded-lg py-1 focus:border-[#6366f1] focus:outline-none"
                  />
                  <button
                    onClick={() => updateQuantity(item.id, 1)}
                    className="w-9 h-9 flex items-center justify-center bg-[#6366f1] text-white hover:bg-[#5558e3] active:scale-95 rounded-lg transition-all font-bold">
                    <Plus className="w-4 h-4" />
                  </button>
                </div>
              </div>
            ))
          )}
        </div>

        {cartItems.length > 0 && (
          <div className="p-4 md:p-6 border-t border-gray-200 space-y-4 bg-gray-50">
            <div className="flex justify-between items-center text-xl md:text-2xl font-bold">
              <span>Total</span>
              <span className="text-[#ec4899]">₱{cartTotal.toLocaleString()}</span>
            </div>

            <div>
              <label className="block text-sm font-semibold text-gray-700 mb-2">Payment Method</label>
              <div className="grid grid-cols-2 gap-2">
                <button
                  onClick={() => {
                    setPaymentMethod('cash');
                    setTransactionNumber('');
                  }}
                  className={`px-4 py-3 rounded-lg font-medium transition-all active:scale-95 ${
                    paymentMethod === 'cash'
                      ? 'bg-[#6366f1] text-white shadow-md'
                      : 'bg-white border-2 border-gray-200 text-gray-700 hover:border-[#6366f1]'
                  }`}>
                  Cash
                </button>
                <button
                  onClick={() => {
                    setPaymentMethod('gcash');
                    setAmountReceived('');
                  }}
                  className={`px-4 py-3 rounded-lg font-medium transition-all active:scale-95 ${
                    paymentMethod === 'gcash'
                      ? 'bg-[#6366f1] text-white shadow-md'
                      : 'bg-white border-2 border-gray-200 text-gray-700 hover:border-[#6366f1]'
                  }`}>
                  G-Cash
                </button>
              </div>
            </div>

            <button
              onClick={handleCheckout}
              className="w-full px-6 py-3.5 md:py-4 bg-[#10b981] text-white rounded-lg hover:bg-[#059669] active:scale-95 transition-all shadow-md font-medium text-lg">
              Checkout
            </button>
            <button
              onClick={clearCart}
              className="w-full px-6 py-2.5 md:py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 active:scale-95 transition-all flex items-center justify-center gap-2 font-medium">
              <Trash2 className="w-4 h-4" />
              Clear Cart
            </button>
          </div>
        )}
      </div>

      {showPaymentModal && (
        <div
          className="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
          onClick={() => {
            setShowPaymentModal(false);
            setTransactionNumber('');
            setAmountReceived('');
          }}>
          <div
            className="bg-white rounded-2xl p-6 md:p-8 max-w-md w-full relative shadow-2xl"
            onClick={(e) => e.stopPropagation()}>
            <button
              onClick={() => {
                setShowPaymentModal(false);
                setTransactionNumber('');
                setAmountReceived('');
              }}
              className="absolute top-4 right-4 p-2 hover:bg-gray-100 rounded-lg transition-colors">
              <X className="w-6 h-6" />
            </button>

            <h2 className="text-2xl font-bold text-slate-900 mb-6">
              {paymentMethod === 'cash' ? 'Cash Payment' : 'G-Cash Payment'}
            </h2>

            <div className="mb-6 p-4 bg-indigo-50 rounded-lg border border-indigo-200">
              <div className="text-sm text-slate-600 mb-1">Total Amount</div>
              <div className="text-3xl font-bold text-indigo-600">₱{cartTotal.toLocaleString()}</div>
            </div>

            {paymentMethod === 'gcash' ? (
              <div className="mb-6">
                <label className="block text-sm font-semibold text-slate-700 mb-2">
                  Transaction Number
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
                  <div className="mb-6 p-4 bg-green-50 rounded-lg border border-green-200">
                    <div className="flex justify-between items-center mb-2">
                      <span className="text-sm text-slate-600">Total Amount</span>
                      <span className="font-semibold text-slate-900">₱{cartTotal.toLocaleString()}</span>
                    </div>
                    <div className="flex justify-between items-center mb-2">
                      <span className="text-sm text-slate-600">Amount Received</span>
                      <span className="font-semibold text-slate-900">₱{receivedAmount.toLocaleString()}</span>
                    </div>
                    <div className="flex justify-between items-center pt-2 border-t border-green-300">
                      <span className="text-sm font-semibold text-slate-700">Change</span>
                      <span className="text-2xl font-bold text-green-600">₱{change.toLocaleString()}</span>
                    </div>
                  </div>
                )}
              </>
            )}

            <div className="flex gap-3">
              <button
                onClick={() => {
                  setShowPaymentModal(false);
                  setTransactionNumber('');
                  setAmountReceived('');
                }}
                className="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 active:scale-95 transition-all font-medium">
                Cancel
              </button>
              <button
                onClick={completeCheckout}
                disabled={
                  (paymentMethod === 'gcash' && !transactionNumber.trim()) ||
                  (paymentMethod === 'cash' && receivedAmount < cartTotal)
                }
                className="flex-1 px-6 py-3 bg-[#10b981] text-white rounded-lg hover:bg-[#059669] active:scale-95 transition-all shadow-md font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                Complete
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
