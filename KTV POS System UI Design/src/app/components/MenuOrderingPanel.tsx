import { X, ShoppingCart, ArrowLeft, Trash2 } from 'lucide-react';
import { useState } from 'react';

interface MenuItem {
  id: string;
  name: string;
  price: number;
  category: 'bowl-meal' | 'silog-meal' | 'sizzling-plate' | 'combo-meal';
}

interface MenuOrderingPanelProps {
  roomId: string;
  onClose: () => void;
  onSubmitOrder: (items: MenuItem[], total: number) => void;
  onBack?: () => void;
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

export function MenuOrderingPanel({ roomId, onClose, onSubmitOrder, onBack }: MenuOrderingPanelProps) {
  const [selectedCategory, setSelectedCategory] = useState<MenuItem['category']>('bowl-meal');
  const [cart, setCart] = useState<Map<string, number>>(new Map());

  const categories = [
    { id: 'bowl-meal', name: 'Bowl Meal', icon: '🍜' },
    { id: 'silog-meal', name: 'Silog Meal', icon: '🍳' },
    { id: 'sizzling-plate', name: 'Sizzling Plate', icon: '🔥' },
    { id: 'combo-meal', name: 'Combo Meal', icon: '🍱' },
  ];

  const filteredItems = menuItems.filter(item => item.category === selectedCategory);

  const addToCart = (item: MenuItem) => {
    const newCart = new Map(cart);
    newCart.set(item.id, (newCart.get(item.id) || 0) + 1);
    setCart(newCart);
  };

  const removeFromCart = (itemId: string) => {
    const newCart = new Map(cart);
    const currentQty = newCart.get(itemId) || 0;
    if (currentQty > 1) {
      newCart.set(itemId, currentQty - 1);
    } else {
      newCart.delete(itemId);
    }
    setCart(newCart);
  };

  const removeItemCompletely = (itemId: string) => {
    const newCart = new Map(cart);
    newCart.delete(itemId);
    setCart(newCart);
  };

  const cartTotal = Array.from(cart.entries()).reduce((total, [itemId, qty]) => {
    const item = menuItems.find(i => i.id === itemId);
    return total + (item ? item.price * qty : 0);
  }, 0);

  const handleSubmit = () => {
    const items = Array.from(cart.entries())
      .map(([itemId]) => menuItems.find(i => i.id === itemId))
      .filter((item): item is MenuItem => item !== undefined);
    onSubmitOrder(items, cartTotal);
    if (onBack) {
      onBack();
    } else {
      onClose();
    }
  };

  return (
    <div
      className="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
      onClick={onClose}>
      <div
        className="bg-white border-2 border-gray-200 rounded-2xl max-w-6xl w-full h-[90vh] flex flex-col relative shadow-2xl"
        onClick={(e) => e.stopPropagation()}>
        <div className="p-6 border-b border-gray-200 flex items-center justify-between bg-gray-50">
          <div className="flex items-center gap-3">
            {onBack && (
              <button
                onClick={onBack}
                className="p-2 hover:bg-gray-200 rounded-lg transition-colors active:scale-95"
                title="Back to room details">
                <ArrowLeft className="w-6 h-6" />
              </button>
            )}
            <h2 className="text-2xl md:text-3xl font-bold">Add Order - Room {roomId}</h2>
          </div>
          <button
            onClick={onClose}
            className="p-2 hover:bg-gray-200 rounded-lg transition-colors active:scale-95">
            <X className="w-6 h-6" />
          </button>
        </div>

        <div className="flex flex-1 overflow-hidden">
          <div className="w-48 border-r border-gray-200 p-4 space-y-2 bg-gray-50">
            {categories.map(cat => (
              <button
                key={cat.id}
                onClick={() => setSelectedCategory(cat.id as MenuItem['category'])}
                className={`w-full p-4 rounded-lg text-left transition-all active:scale-95 font-medium ${
                  selectedCategory === cat.id
                    ? 'bg-[#6366f1] text-white shadow-md'
                    : 'bg-white hover:bg-gray-100 border border-gray-200'
                }`}>
                <div className="text-2xl mb-1">{cat.icon}</div>
                <div className="text-sm">{cat.name}</div>
              </button>
            ))}
          </div>

          <div className="flex-1 p-6 overflow-y-auto">
            <div className="grid grid-cols-2 lg:grid-cols-3 gap-4">
              {filteredItems.map(item => (
                <div
                  key={item.id}
                  className="p-6 rounded-xl border-2 border-gray-200 bg-white hover:border-[#6366f1] hover:shadow-md transition-all cursor-pointer active:scale-95"
                  onClick={() => addToCart(item)}>
                  <h4 className="mb-2 font-semibold text-slate-900 line-clamp-2 min-h-[3rem]">{item.name}</h4>
                  <div className="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">₱{item.price.toLocaleString()}</div>
                  {cart.has(item.id) && (
                    <div className="mt-3 flex items-center justify-between bg-[#6366f1] text-white px-3 py-2 rounded-lg shadow-sm">
                      <button
                        onClick={(e) => {
                          e.stopPropagation();
                          removeFromCart(item.id);
                        }}
                        className="text-xl font-bold w-6 h-6 flex items-center justify-center">-</button>
                      <span className="font-semibold">{cart.get(item.id)}</span>
                      <button
                        onClick={(e) => {
                          e.stopPropagation();
                          addToCart(item);
                        }}
                        className="text-xl font-bold w-6 h-6 flex items-center justify-center">+</button>
                    </div>
                  )}
                </div>
              ))}
            </div>
          </div>

          {cart.size > 0 && (
            <div className="w-80 border-l border-gray-200 bg-gray-50 flex flex-col">
              <div className="p-4 border-b border-gray-200">
                <div className="flex items-center gap-3">
                  <ShoppingCart className="w-5 h-5 text-[#6366f1]" />
                  <span className="text-lg font-semibold">Order Summary</span>
                </div>
              </div>

              <div className="flex-1 overflow-y-auto p-4">
                <div className="space-y-2">
                  {Array.from(cart.entries()).map(([itemId, qty]) => {
                    const item = menuItems.find(i => i.id === itemId);
                    if (!item) return null;
                    return (
                      <div key={itemId} className="bg-white rounded-lg border border-gray-200 p-3 shadow-sm">
                        <div className="flex justify-between items-start gap-2 mb-2">
                          <div className="flex-1">
                            <div className="font-medium text-sm">{item.name}</div>
                            <div className="text-xs text-gray-500">₱{item.price} x {qty}</div>
                          </div>
                          <button
                            onClick={() => removeItemCompletely(itemId)}
                            className="p-1 hover:bg-red-100 rounded transition-colors text-red-600"
                            title="Remove item">
                            <Trash2 className="w-4 h-4" />
                          </button>
                        </div>
                        <div className="font-semibold text-[#6366f1]">₱{(item.price * qty).toLocaleString()}</div>
                      </div>
                    );
                  })}
                </div>
              </div>

              <div className="p-4 border-t border-gray-200 bg-white space-y-3">
                <div className="flex items-center justify-between text-xl font-bold">
                  <span>Total</span>
                  <span className="text-[#ec4899]">₱{cartTotal.toLocaleString()}</span>
                </div>

                <button
                  onClick={handleSubmit}
                  className="w-full px-6 py-3 bg-[#10b981] text-white rounded-lg hover:bg-[#059669] active:scale-95 transition-all shadow-md font-medium">
                  Submit Order
                </button>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
