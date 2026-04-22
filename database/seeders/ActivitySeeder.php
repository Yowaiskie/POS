<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Room;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = Room::all()->keyBy('name');
        $today = now()->startOfDay();

        $activities = [
            [
                'room' => 'Room 2',
                'action' => 'Order added',
                'amount_label' => '+₱35',
                'amount_type' => 'money',
                'occurred_at' => $today->copy()->addHours(14)->addMinutes(25),
            ],
            [
                'room' => 'Room 1',
                'action' => 'Session extended',
                'amount_label' => '+30 min',
                'amount_type' => 'time',
                'occurred_at' => $today->copy()->addHours(14)->addMinutes(15),
            ],
            [
                'room' => 'Room 5',
                'action' => 'New session started',
                'amount_label' => '₱150',
                'amount_type' => 'charge',
                'occurred_at' => $today->copy()->addHours(13)->addMinutes(58),
            ],
        ];

        foreach ($activities as $activity) {
            $room = $rooms->get($activity['room']);

            Activity::updateOrCreate(
                ['action' => $activity['action'], 'occurred_at' => $activity['occurred_at']],
                [
                    'room_id' => $room?->id,
                    'amount_label' => $activity['amount_label'],
                    'amount_type' => $activity['amount_type'],
                ]
            );
        }
    }
}
