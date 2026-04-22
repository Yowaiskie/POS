<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Collection;

class ReportController extends Controller
{
    public function index()
    {
        $period = request('period', 'daily');
        [$start, $end] = match ($period) {
            'weekly' => [now()->startOfWeek(), now()->endOfWeek()],
            'monthly' => [now()->startOfMonth(), now()->endOfMonth()],
            default => [now()->startOfDay(), now()->endOfDay()],
        };

        $orders = Order::with(['items', 'user'])
            ->where('status', 'paid')
            ->whereBetween('closed_at', [$start, $end])
            ->get();

        $totalSales = $orders->sum(fn (Order $order) => $order->total_amount);
        $roomSales = $orders->where('order_type', 'room')->sum(fn (Order $order) => $order->total_amount);
        $shortSales = $orders->where('order_type', 'short')->sum(fn (Order $order) => $order->total_amount);

        $totalSales = $totalSales ?: 0;
        $roomPercent = $totalSales > 0 ? round(($roomSales / $totalSales) * 100) : 0;
        $shortPercent = $totalSales > 0 ? round(($shortSales / $totalSales) * 100) : 0;

        $topSelling = $orders
            ->flatMap(fn (Order $order) => $order->items)
            ->groupBy('name')
            ->map(function (Collection $items) {
                $qty = $items->sum('quantity');
                $revenue = $items->sum(fn (OrderItem $item) => $item->unit_price * $item->quantity);

                return [
                    'name' => $items->first()->name,
                    'qty' => $qty,
                    'rev' => $revenue,
                ];
            })
            ->sortByDesc('rev')
            ->take(5)
            ->values();

        $staffPerformance = $orders
            ->filter(fn (Order $order) => $order->user)
            ->groupBy('user_id')
            ->map(function (Collection $staffOrders) {
                $user = $staffOrders->first()->user;
                $sales = $staffOrders->sum(fn (Order $order) => $order->total_amount);

                return [
                    'name' => $user->name,
                    'sales' => $sales,
                ];
            })
            ->sortByDesc('sales')
            ->values();

        $staffPerformance = $staffPerformance->map(function (array $staff, int $index) {
            $label = $index === 0 ? 'Top Performer' : ($index === 1 ? 'Rising Star' : 'Consistent');

            return [
                'name' => $staff['name'],
                'sales' => $staff['sales'],
                'perf' => $label,
            ];
        });

        return view('reports.index', [
            'period' => $period,
            'totalSales' => $totalSales,
            'roomSales' => $roomSales,
            'shortSales' => $shortSales,
            'roomPercent' => $roomPercent,
            'shortPercent' => $shortPercent,
            'topSelling' => $topSelling,
            'staffPerformance' => $staffPerformance,
        ]);
    }
}
