import { useState } from 'react';
import { Plus, X, Trash2 } from 'lucide-react';

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

export function MenuPage() {
  const [selectedCategory, setSelectedCategory] = useState<MenuItem['category']>('bowl-meal');
  const [items, setItems] = useState<MenuItem[]>(menuItems);
  const [editingItem, setEditingItem] = useState<MenuItem | null>(null);
  const [showModal, setShowModal] = useState(false);
  const [isAdding, setIsAdding] = useState(false);
  const [formData, setFormData] = useState({ name: '', price: 0, category: 'bowl-meal' as MenuItem['category'] });

  const categories = [
    { id: 'bowl-meal' as const, name: 'Bowl Meal', count: items.filter(i => i.category === 'bowl-meal').length },
    { id: 'silog-meal' as const, name: 'Silog Meal', count: items.filter(i => i.category === 'silog-meal').length },
    { id: 'sizzling-plate' as const, name: 'Sizzling Plate', count: items.filter(i => i.category === 'sizzling-plate').length },
    { id: 'combo-meal' as const, name: 'Combo Meal', count: items.filter(i => i.category === 'combo-meal').length },
  ];

  const filteredItems = items.filter(item => item.category === selectedCategory);

  const handleEdit = (item: MenuItem) => {
    setEditingItem(item);
    setFormData({ name: item.name, price: item.price, category: item.category });
    setIsAdding(false);
    setShowModal(true);
  };

  const handleAdd = () => {
    setEditingItem(null);
    setFormData({ name: '', price: 0, category: selectedCategory });
    setIsAdding(true);
    setShowModal(true);
  };

  const handleDelete = (itemId: string) => {
    if (confirm('Are you sure you want to delete this item?')) {
      setItems(items.filter(i => i.id !== itemId));
    }
  };

  const handleSave = () => {
    if (!formData.name.trim() || formData.price <= 0) {
      alert('Please enter a valid name and price');
      return;
    }

    if (isAdding) {
      const newItem: MenuItem = {
        id: (Math.max(...items.map(i => parseInt(i.id))) + 1).toString(),
        name: formData.name,
        price: formData.price,
        category: formData.category,
      };
      setItems([...items, newItem]);
    } else if (editingItem) {
      setItems(items.map(i => i.id === editingItem.id
        ? { ...i, name: formData.name, price: formData.price, category: formData.category }
        : i
      ));
    }

    setShowModal(false);
    setFormData({ name: '', price: 0, category: 'bowl-meal' });
    setEditingItem(null);
  };

  return (
    <div className="p-4 md:p-8 max-w-[1600px] mx-auto">
      <div className="mb-8">
        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
          <div>
            <h1 className="text-3xl md:text-4xl font-bold text-slate-900 mb-2">Menu Management</h1>
            <p className="text-slate-600">Manage your products, packages, and pricing</p>
          </div>
          <button
            onClick={handleAdd}
            className="px-6 py-3 bg-[#10b981] text-white rounded-lg hover:bg-[#059669] active:scale-95 transition-all shadow-md font-medium flex items-center gap-2 w-full sm:w-auto justify-center">
            <Plus className="w-5 h-5" />
            Add Item
          </button>
        </div>
      </div>

      <div className="flex gap-2 md:gap-3 mb-6 md:mb-8 overflow-x-auto pb-2">
        {categories.map(cat => (
          <button
            key={cat.id}
            onClick={() => setSelectedCategory(cat.id)}
            className={`flex-shrink-0 px-4 md:px-6 py-3 rounded-lg transition-all active:scale-95 font-medium ${
              selectedCategory === cat.id
                ? 'bg-[#6366f1] text-white shadow-md'
                : 'bg-white border-2 border-gray-200 hover:border-[#6366f1] hover:shadow-sm'
            }`}>
            <div className="mb-1 font-semibold">{cat.name}</div>
            <div className="text-xs md:text-sm opacity-75">{cat.count} items</div>
          </button>
        ))}
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
        {filteredItems.map(item => (
          <div
            key={item.id}
            className="bg-white rounded-xl border-2 border-slate-200 p-5 md:p-6 transition-all hover:shadow-lg hover:border-indigo-400">
            <h3 className="text-lg md:text-xl mb-3 font-semibold text-slate-900">{item.name}</h3>
            <div className="text-2xl md:text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4">₱{item.price.toLocaleString()}</div>
            <div className="text-xs md:text-sm text-slate-600 capitalize mb-4 bg-slate-100 px-3 py-1 rounded-full inline-block">
              {item.category.replace('-', ' ')}
            </div>
            <div className="flex gap-2 mt-4">
              <button
                onClick={() => handleEdit(item)}
                className="flex-1 px-4 py-2 bg-[#6366f1] text-white rounded-lg hover:bg-[#5558e3] active:scale-95 transition-all shadow-sm text-sm font-medium">
                Edit
              </button>
              <button
                onClick={() => handleDelete(item.id)}
                className="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 active:scale-95 transition-all text-sm font-medium flex items-center gap-1">
                <Trash2 className="w-4 h-4" />
              </button>
            </div>
          </div>
        ))}
      </div>

      {showModal && (
        <div className="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
          <div className="bg-white rounded-2xl p-6 md:p-8 max-w-md w-full relative shadow-2xl">
            <button
              onClick={() => {
                setShowModal(false);
                setFormData({ name: '', price: 0, category: 'bowl-meal' });
                setEditingItem(null);
              }}
              className="absolute top-4 right-4 p-2 hover:bg-gray-100 rounded-lg transition-colors">
              <X className="w-6 h-6" />
            </button>

            <h2 className="text-2xl font-bold text-slate-900 mb-6">
              {isAdding ? 'Add New Item' : 'Edit Item'}
            </h2>

            <div className="space-y-4">
              <div>
                <label className="block text-sm font-semibold text-slate-700 mb-2">
                  Item Name
                </label>
                <input
                  type="text"
                  value={formData.name}
                  onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                  placeholder="Enter item name"
                  className="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-indigo-500 focus:outline-none"
                />
              </div>

              <div>
                <label className="block text-sm font-semibold text-slate-700 mb-2">
                  Price
                </label>
                <div className="relative">
                  <span className="absolute left-4 top-1/2 -translate-y-1/2 text-slate-600 font-semibold">₱</span>
                  <input
                    type="number"
                    value={formData.price || ''}
                    onChange={(e) => setFormData({ ...formData, price: parseFloat(e.target.value) || 0 })}
                    placeholder="0"
                    className="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:border-indigo-500 focus:outline-none"
                  />
                </div>
              </div>

              <div>
                <label className="block text-sm font-semibold text-slate-700 mb-2">
                  Category
                </label>
                <select
                  value={formData.category}
                  onChange={(e) => setFormData({ ...formData, category: e.target.value as MenuItem['category'] })}
                  className="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-indigo-500 focus:outline-none">
                  <option value="bowl-meal">Bowl Meal</option>
                  <option value="silog-meal">Silog Meal</option>
                  <option value="sizzling-plate">Sizzling Plate</option>
                  <option value="combo-meal">Combo Meal</option>
                </select>
              </div>
            </div>

            <div className="flex gap-3 mt-6">
              <button
                onClick={() => {
                  setShowModal(false);
                  setFormData({ name: '', price: 0, category: 'bowl-meal' });
                  setEditingItem(null);
                }}
                className="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 active:scale-95 transition-all font-medium">
                Cancel
              </button>
              <button
                onClick={handleSave}
                className="flex-1 px-6 py-3 bg-[#6366f1] text-white rounded-lg hover:bg-[#5558e3] active:scale-95 transition-all shadow-md font-medium">
                {isAdding ? 'Add' : 'Save'}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
