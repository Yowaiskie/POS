<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ShiftService
{
    public function start(User $user, float $startingCash): Shift
    {
        if ($user->activeShift) {
            throw new \Exception('User already has an open shift.');
        }

        return DB::transaction(function () use ($user, $startingCash) {
            return Shift::create([
                'user_id' => $user->id,
                'start_time' => now(),
                'starting_cash' => $startingCash,
                'status' => 'open',
            ]);
        });
    }

    public function close(Shift $shift, float $actualCash, float $actualGcash = 0): Shift
    {
        if ($shift->status !== 'open') {
            throw new \Exception('This shift is not open.');
        }

        return DB::transaction(function () use ($shift, $actualCash, $actualGcash) {
            $totals = $this->calculateTotals($shift);
            
            $expectedCash = $shift->starting_cash + $totals['cash_sales'];
            $expectedGcash = $totals['gcash_sales'];
            
            $differenceAmount = ($actualCash - $expectedCash) + ($actualGcash - $expectedGcash);
            
            $differenceType = 'matched';
            if ($differenceAmount > 0) {
                $differenceType = 'over';
            } elseif ($differenceAmount < 0) {
                $differenceType = 'short';
            }

            $shift->update([
                'end_time' => now(),
                'expected_cash' => $expectedCash,
                'actual_cash' => $actualCash,
                'expected_gcash' => $expectedGcash,
                'actual_gcash' => $actualGcash,
                'difference_amount' => $differenceAmount,
                'difference_type' => $differenceType,
                'status' => 'closed',
            ]);

            return $shift;
        });
    }

    public function forceClose(Shift $shift, User $admin, ?string $notes = null): Shift
    {
        if ($shift->status !== 'open') {
            throw new \Exception('This shift is not open.');
        }

        return DB::transaction(function () use ($shift, $admin, $notes) {
            $totals = $this->calculateTotals($shift);
            $expectedCash = $shift->starting_cash + $totals['cash_sales'];
            $expectedGcash = $totals['gcash_sales'];
            
            $shift->update([
                'end_time' => now(),
                'expected_cash' => $expectedCash,
                'actual_cash' => $expectedCash, // Automatically match expected cash to prevent skewed analytics
                'expected_gcash' => $expectedGcash,
                'actual_gcash' => $expectedGcash, // Automatically match expected GCash
                'difference_amount' => 0,
                'difference_type' => 'matched',
                'status' => 'force_closed',
                'notes' => "Force closed by admin ({$admin->name}). " . $notes,
            ]);

            return $shift;
        });
    }

    public function calculateTotals(Shift $shift): array
    {
        $orders = Order::where('shift_id', $shift->id)->where('status', 'paid')->get();

        $cashSales = 0;
        $gcashSales = 0;
        $roomSales = 0;
        $shortSales = 0;
        $discounts = 0;

        foreach ($orders as $order) {
            $amount = collect($order->items)->sum(function ($item) {
                return $item->unit_price * $item->quantity;
            });

            if ($order->payment_method === 'cash') {
                $cashSales += $amount;
            } elseif ($order->payment_method === 'gcash') {
                $gcashSales += $amount;
            }

            if ($order->order_type === 'room') {
                $roomSales += $amount;
            } elseif ($order->order_type === 'short') {
                $shortSales += $amount;
            }

            // Note: Since discounts/promos might be handled differently, 
            // adjust this if you have a specific discount field. Currently relying on promo_price.
        }

        $voidedItems = OrderItem::with('menuItem')->where('void_shift_id', $shift->id)->get();
        $voidsTotal = $voidedItems->sum(function ($item) {
            return $item->unit_price * $item->quantity;
        });

        return [
            'cash_sales' => collect($orders)->where('payment_method', 'cash')->sum(function($o) { return $o->total_amount; }),
            'gcash_sales' => collect($orders)->where('payment_method', 'gcash')->sum(function($o) { return $o->total_amount; }),
            'room_sales' => collect($orders)->where('order_type', 'room')->sum(function($o) { return $o->total_amount; }),
            'short_sales' => collect($orders)->where('order_type', 'short')->sum(function($o) { return $o->total_amount; }),
            'voids_total' => $voidsTotal,
            'voids_count' => $voidedItems->sum('quantity'),
            // 'discounts' => ... (add if necessary based on db structure)
        ];
    }
}
