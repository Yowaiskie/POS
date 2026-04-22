<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Collection;

use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        $period = request('period', 'daily');
        $data = $this->getReportData($period);

        return view('reports.index', $data);
    }

    public function pdf()
    {
        $period = request('period', 'daily');
        // Top 10 for PDF, no pagination for transactions
        $data = $this->getReportData($period, 10, false);
        $data['generated_at'] = now()->format('M d, Y h:i A');

        $pdf = Pdf::loadView('reports.pdf', $data);
        
        $filename = "POS-Report-{$period}-" . now()->format('Y-m-d') . ".pdf";
        return $pdf->download($filename);
    }

    private function getReportData($period, $limitTopSelling = 5, $paginate = true)
    {
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
            ->take($limitTopSelling)
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

        $query = Order::with(['items', 'user'])
            ->where('status', 'paid')
            ->whereBetween('closed_at', [$start, $end])
            ->latest('closed_at');

        $recentOrders = $paginate 
            ? $query->paginate(10)->withQueryString()
            : $query->get();

        return [
            'period' => $period,
            'start' => $start,
            'end' => $end,
            'totalSales' => $totalSales,
            'roomSales' => $roomSales,
            'shortSales' => $shortSales,
            'roomPercent' => $roomPercent,
            'shortPercent' => $shortPercent,
            'topSelling' => $topSelling,
            'staffPerformance' => $staffPerformance,
            'recentOrders' => $recentOrders,
            'totalTransactions' => $orders->count(),
        ];
    }
}
