<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\RoomSession;
use Illuminate\Database\Seeder;

class RoomSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $rooms = Room::orderBy('name')->get()->keyBy('name');

        $sessions = [
            ['room' => 'Room 1', 'status' => 'active', 'ends_at' => $now->copy()->addMinutes(45)],
            ['room' => 'Room 2', 'status' => 'warning', 'ends_at' => $now->copy()->addMinutes(8)],
            ['room' => 'Room 3', 'status' => 'overtime', 'ends_at' => $now->copy()->subMinutes(5)],
            ['room' => 'Room 5', 'status' => 'active', 'ends_at' => $now->copy()->addMinutes(90)],
        ];

        foreach ($sessions as $session) {
            $room = $rooms->get($session['room']);

            if (!$room) {
                continue;
            }

            RoomSession::updateOrCreate(
                ['room_id' => $room->id],
                [
                    'status' => $session['status'],
                    'started_at' => $now->copy()->subMinutes(30),
                    'ends_at' => $session['ends_at'],
                ]
            );
        }
    }
}
