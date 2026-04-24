<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Room;
use App\Models\RoomSession;
use App\Models\PromoSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RoomPricing;
use App\Services\OrderFlowService;
use Illuminate\Support\Str;

class RoomController extends Controller
{
    protected $orderFlowService;

    public function __construct(OrderFlowService $orderFlowService)
    {
        $this->orderFlowService = $orderFlowService;
    }

    public function index()
    {
        $rooms = Room::with(['activeSession.orders.items.menuItem'])
            ->orderBy('name')
            ->get();

        $categories = MenuCategory::withCount('items')->orderBy('id')->get();
        $items = MenuItem::with('category')->where('is_active', true)->orderBy('name')->get();
        $promoSets = PromoSet::with('items.menuItem')->where('is_active', true)->get();

        $pricing = RoomPricing::first();

        return view('rooms.index', [
            'rooms' => $rooms,
            'categories' => $categories,
            'items' => $items,
            'promoSets' => $promoSets,
            'pricing' => $pricing,
        ]);
    }

    public function startSession(Request $request, Room $room)
    {
        if ($request->isMethod('get')) {
            return redirect()->route('rooms.index');
        }

        if ($room->activeSession) {
            return back()->with('error', 'Room is already occupied.');
        }

        try {
            $this->orderFlowService->startRoomSession($room, $request->all());
            return back()->with('success', "Session started for {$room->name}");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to start session: ' . $e->getMessage());
        }
    }

    public function extendSession(Request $request, RoomSession $session)
    {
        if ($request->isMethod('post')) {
            $duration = (int) $request->input('duration', 30);
            $newEndsAt = $session->ends_at ? $session->ends_at->addMinutes($duration) : now()->addMinutes($duration);

            $session->update([
                'ends_at' => $newEndsAt,
                'status' => 'active',
            ]);

            return redirect()->route('rooms.index')->with('success', "Extended session by {$duration} minutes.");
        }

        return redirect()->route('rooms.index');
    }

    public function billOut(Request $request, RoomSession $session)
    {
        if ($request->isMethod('get')) {
            return redirect()->route('rooms.index');
        }

        if ($session->status !== 'active') {
            return back()->with('error', 'This session has already been billed out or is not active.');
        }

        if ($request->payment_method === 'gcash') {
            $request->validate([
                'reference_number' => 'required|digits:13',
            ], [
                'reference_number.digits' => 'GCash Reference Number must be exactly 13 digits.',
            ]);
        }

        try {
            $this->orderFlowService->billOutRoom($session, $request->all());
            return back()->with('success', "Room billed out successfully.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
