<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Room;
use App\Models\RoomSession;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with(['activeSession.orders.items'])
            ->orderBy('name')
            ->get();

        return view('rooms.index', ['rooms' => $rooms]);
    }

    public function startSession(Room $room)
    {
        if ($room->activeSession) {
            return back()->with('error', 'Room is already occupied.');
        }

        $room->sessions()->create([
            'started_at' => now(),
            'end_time' => now()->addHour(), // Default 1 hour
            'status' => 'active',
        ]);

        return back()->with('success', "Session started for {$room->name}");
    }

    public function extendSession(RoomSession $session)
    {
        $session->update([
            'end_time' => $session->end_time->addMinutes(30),
            'status' => 'active', // Reset to active if it was warning/overtime
        ]);

        return back()->with('success', "Extended session by 30 minutes.");
    }

    public function billOut(RoomSession $session)
    {
        $session->update([
            'status' => 'completed',
            'ended_at' => now(),
        ]);

        return back()->with('success', "Room billed out successfully.");
    }
}
