<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $categories = MenuCategory::orderBy('id')->get();
        $selectedCategory = request('category', 'all');

        $itemsQuery = MenuItem::with('category')->orderBy('name');

        if ($selectedCategory !== 'all') {
            $itemsQuery->whereHas('category', function ($query) use ($selectedCategory) {
                $query->where('slug', $selectedCategory);
            });
        }

        $items = $itemsQuery->get();

        $stats = [
            'total'        => MenuItem::count(),
            'in_stock'     => MenuItem::where(function ($q) {
                $q->whereNull('stock_quantity')->orWhere('stock_quantity', '>', 5);
            })->count(),
            'low_stock'    => MenuItem::whereBetween('stock_quantity', [1, 5])->count(),
            'out_of_stock' => MenuItem::where('stock_quantity', 0)->count(),
        ];

        return view('inventory.index', compact('categories', 'items', 'selectedCategory', 'stats'));
    }

    public function update(Request $request, MenuItem $item)
    {
        $validated = $request->validate([
            'stock_quantity' => 'nullable|integer|min:0',
            'unlimited'      => 'nullable|boolean',
        ]);

        $isUnlimited = $request->boolean('unlimited');
        $item->update([
            'stock_quantity' => $isUnlimited ? null : $validated['stock_quantity'],
        ]);

        return back()->with('success', "Stock for \"{$item->name}\" updated successfully.");
    }
}
