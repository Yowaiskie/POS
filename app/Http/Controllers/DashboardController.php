<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Order;
use App\Models\Room;
use App\Models\RoomSession;

class DashboardController extends Controller
{
    public function index()
    {
        $activeStatuses = ['active', 'warning', 'overtime'];

        $activeRoomsCount = RoomSession::whereIn('status', $activeStatuses)
            ->distinct('room_id')
            ->count('room_id');

        $availableRoomsCount = Room::whereDoesntHave('sessions', function ($query) use ($activeStatuses) {
            $query->whereIn('status', $activeStatuses);
        })->count();

        $totalSalesToday = Order::with('items')
            ->where('status', 'paid')
            ->whereDate('closed_at', today())
            ->get()
            ->sum(fn (Order $order) => $order->total_amount);

        $activeSessions = RoomSession::with(['room', 'orders.items'])
            ->whereIn('status', $activeStatuses)
            ->orderByDesc('started_at')
            ->take(3)
            ->get()
            ->map(function (RoomSession $session) {
                $bill = $session->orders->sum(fn (Order $order) => $order->total_amount);

                return [
                    'room' => $session->room?->name ?? 'Room',
                    'status' => $session->status,
                    'timer' => $session->timer,
                    'started_at' => $session->started_at?->toIso8601String(),
                    'ends_at' => $session->ends_at?->toIso8601String(),
                    'bill' => $bill,
                ];
            });

        $recentActivities = Activity::with('room')
            ->orderByDesc('occurred_at')
            ->take(5)
            ->get()
            ->map(function (Activity $activity) {
                $amountClass = match ($activity->amount_type) {
                    'money' => 'text-emerald-600',
                    'time' => 'text-sky-600',
                    default => 'text-pink-600',
                };

                return [
                    'time' => $activity->occurred_at?->format('H:i') ?? '--:--',
                    'room' => $activity->room?->name ?? '—',
                    'action' => $activity->action,
                    'amount_label' => $activity->amount_label ?? '',
                    'amount_class' => $amountClass,
                ];
            });

        return view('dashboard', [
            'activeRoomsCount' => $activeRoomsCount,
            'availableRoomsCount' => $availableRoomsCount,
            'totalSalesToday' => $totalSalesToday,
            'activeSessions' => $activeSessions,
            'recentActivities' => $recentActivities,
        ]);
    }
}
