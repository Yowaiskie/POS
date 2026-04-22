<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $categories = MenuCategory::withCount('items')->orderBy('id')->get();
        $selectedCategory = request('category', $categories->first()?->slug);

        $itemsQuery = MenuItem::with('category');
        if ($selectedCategory) {
            $itemsQuery->whereHas('category', function ($query) use ($selectedCategory) {
                $query->where('slug', $selectedCategory);
            });
        }

        $items = $itemsQuery->orderBy('name')->paginate(10)->withQueryString();

        return view('menu.index', [
            'categories'      => $categories,
            'items'           => $items,
            'selectedCategory' => $selectedCategory,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'price'          => 'required|numeric|min:0',
            'category_id'    => 'required|exists:menu_categories,id',
            'stock_quantity' => 'nullable|integer|min:0',
        ]);

        // Treat "unlimited" checkbox — if not present, keep value; if no value given, null = unlimited
        if ($request->boolean('unlimited')) {
            $validated['stock_quantity'] = null;
        }

        MenuItem::create($validated);

        return back()->with('success', 'Menu item added successfully.');
    }

    public function update(Request $request, MenuItem $item)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'price'          => 'required|numeric|min:0',
            'category_id'    => 'required|exists:menu_categories,id',
            'stock_quantity' => 'nullable|integer|min:0',
        ]);

        if ($request->boolean('unlimited')) {
            $validated['stock_quantity'] = null;
        }

        $item->update($validated);

        return back()->with('success', 'Menu item updated successfully.');
    }

    public function destroy(MenuItem $item)
    {
        $item->delete();
        return back()->with('success', 'Menu item deleted successfully.');
    }
}
