<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PromoSetController extends Controller
{
    public function index()
    {
        $promoSets = \App\Models\PromoSet::with('items.menuItem')->orderBy('name')->paginate(10);
        $menuItems = \App\Models\MenuItem::where('is_active', true)->orderBy('name')->get();

        return view('promo-sets.index', compact('promoSets', 'menuItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_hours' => 'required|integer|min:1',
            'items' => 'required|array',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $promoSet = \App\Models\PromoSet::create([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'duration_hours' => $validated['duration_hours'],
            'is_active' => true,
        ]);

        foreach ($validated['items'] as $item) {
            $promoSet->items()->create($item);
        }

        return back()->with('success', 'Promo Set created successfully.');
    }

    public function update(Request $request, \App\Models\PromoSet $promoSet)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_hours' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'items' => 'required|array',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $promoSet->update([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'duration_hours' => $validated['duration_hours'],
            'is_active' => $request->has('is_active'),
        ]);

        $promoSet->items()->delete();
        foreach ($validated['items'] as $item) {
            $promoSet->items()->create($item);
        }

        return back()->with('success', 'Promo Set updated successfully.');
    }

    public function destroy(\App\Models\PromoSet $promoSet)
    {
        $promoSet->delete();
        return back()->with('success', 'Promo Set deleted successfully.');
    }
}
