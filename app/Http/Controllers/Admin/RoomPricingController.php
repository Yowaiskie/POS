<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomPricing;
use Illuminate\Http\Request;

class RoomPricingController extends Controller
{
    /**
     * Show the pricing configuration.
     */
    public function index()
    {
        $pricing = RoomPricing::first();
        return view('admin.room_pricing.index', compact('pricing'));
    }

    /**
     * Show the edit form.
     */
    public function edit()
    {
        $pricing = RoomPricing::first();
        return view('admin.room_pricing.edit', compact('pricing'));
    }

    /**
     * Update the pricing configuration.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'base_rate_per_hour' => 'required|numeric|min:0',
            'billing_unit_minutes' => 'required|integer|min:1',
            'grace_period_minutes' => 'required|integer|min:0',
            'per_room_rate' => 'required|boolean',
            'price_30_min' => 'required|numeric|min:0',
            'price_60_min' => 'required|numeric|min:0',
            'overtime_unit_minutes' => 'required|integer|min:1',
            'overtime_unit_price' => 'required|numeric|min:0',
        ]);

        $pricing = RoomPricing::first();
        if (! $pricing) {
            $pricing = RoomPricing::create($validated);
        } else {
            $pricing->update($validated);
        }

        // Increment version for audit purposes
        $pricing->increment('version');

        return redirect()->route('admin.room_pricing.index')
                         ->with('success', 'Room pricing configuration updated successfully.');
    }
}
