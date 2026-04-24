<?php

namespace App\Services;

use App\Models\MenuItem;
use App\Models\OrderItem;
use Exception;

class InventoryService
{
    /**
     * Validate if a menu item has enough stock.
     */
    public function validateStock(MenuItem $menuItem, int $quantity): void
    {
        if ($menuItem->stock_quantity !== null && $menuItem->stock_quantity < $quantity) {
            throw new Exception("Not enough stock for \"{$menuItem->name}\". Only {$menuItem->stock_quantity} available.");
        }
    }

    /**
     * Deduct stock for an order item.
     */
    public function deductStock(OrderItem $orderItem): void
    {
        if ($orderItem->is_stock_deducted) {
            return;
        }

        $menuItem = $orderItem->menuItem;
        if ($menuItem && $menuItem->stock_quantity !== null) {
            $this->validateStock($menuItem, $orderItem->quantity);
            $menuItem->decrement('stock_quantity', $orderItem->quantity);
        }

        $orderItem->update(['is_stock_deducted' => true]);
    }

    /**
     * Return stock for an order item.
     */
    public function returnStock(OrderItem $orderItem): void
    {
        if (!$orderItem->is_stock_deducted) {
            return;
        }

        $menuItem = $orderItem->menuItem;
        if ($menuItem && $menuItem->stock_quantity !== null) {
            $menuItem->increment('stock_quantity', $orderItem->quantity);
        }

        $orderItem->update(['is_stock_deducted' => false]);
    }

    /**
     * Adjust stock for quantity changes.
     */
    public function adjustStock(OrderItem $orderItem, int $newQuantity): void
    {
        if (!$orderItem->is_stock_deducted) {
            return;
        }

        $delta = $newQuantity - $orderItem->quantity;
        if ($delta === 0) return;

        $menuItem = $orderItem->menuItem;
        if (!$menuItem || $menuItem->stock_quantity === null) return;

        if ($delta > 0) {
            $this->validateStock($menuItem, $delta);
            $menuItem->decrement('stock_quantity', $delta);
        } else {
            $menuItem->increment('stock_quantity', abs($delta));
        }
    }

    /**
     * Void an order item.
     */
    public function voidItem(OrderItem $item, $user): void
    {
        if ($item->is_voided) {
            return;
        }

        // If stock was already deducted, only admin can restore it
        if ($item->is_stock_deducted) {
            if ($user->position !== 'Admin') {
                throw new Exception("Only Admin can void already deducted items.");
            }

            $this->returnStock($item);
        }

        $item->update([
            'is_voided' => true,
            'voided_at' => now(),
            'voided_by' => $user->id,
        ]);
    }
}
