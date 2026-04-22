<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = [
            'Room 1',
            'Room 2',
            'Room 3',
            'Room 4',
            'Room 5',
            'Room 6',
        ];

        foreach ($rooms as $roomName) {
            Room::updateOrCreate(['name' => $roomName]);
        }
    }
}
