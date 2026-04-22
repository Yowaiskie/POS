<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RoomSession;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $today = now()->startOfDay();
        $menuItems = MenuItem::all()->keyBy('name');
        $sessions = RoomSession::with('room')->get()->keyBy(fn (RoomSession $session) => $session->room?->name);

        $cashier = User::where('position', 'Cashier')->first();
        $supervisor = User::where('position', 'Supervisor')->first();

        if (!$menuItems->count()) {
            return;
        }

        $shortOpen = Order::updateOrCreate(
            ['order_type' => 'short', 'status' => 'open'],
            [
                'user_id' => $cashier?->id,
                'created_at' => $now->copy()->subMinutes(10),
                'updated_at' => $now->copy()->subMinutes(5),
            ]
        );

        $this->syncItems($shortOpen, [
            ['name' => 'Cheesy Katsu', 'qty' => 1],
            ['name' => 'Hungarian', 'qty' => 1],
        ], $menuItems);

        $roomOrder = Order::updateOrCreate(
            ['order_type' => 'room', 'status' => 'open', 'room_session_id' => $sessions->get('Room 1')?->id],
            [
                'user_id' => $cashier?->id,
                'created_at' => $now->copy()->subMinutes(40),
            ]
        );

        $this->syncItems($roomOrder, [
            ['name' => 'Cheesy Karaage', 'qty' => 2],
            ['name' => 'Buffalo Pops', 'qty' => 1],
        ], $menuItems);

        $paidRoomOrder = Order::updateOrCreate(
            ['order_type' => 'room', 'status' => 'paid', 'closed_at' => $today->copy()->addHours(13)],
            [
                'room_session_id' => $sessions->get('Room 2')?->id,
                'user_id' => $supervisor?->id,
                'payment_method' => 'Cash',
                'created_at' => $today->copy()->addHours(12),
            ]
        );

        $this->syncItems($paidRoomOrder, [
            ['name' => 'Mushroom Gravy', 'qty' => 1],
            ['name' => 'Chicken Skin', 'qty' => 2],
        ], $menuItems);

        $paidShortOrder = Order::updateOrCreate(
            ['order_type' => 'short', 'status' => 'paid', 'closed_at' => $today->copy()->addHours(11)],
            [
                'user_id' => $cashier?->id,
                'payment_method' => 'G-Cash',
                'created_at' => $today->copy()->addHours(10),
            ]
        );

        $this->syncItems($paidShortOrder, [
            ['name' => 'Cheesy Katsu', 'qty' => 3],
            ['name' => 'Creamy Salted Egg Chicken', 'qty' => 1],
        ], $menuItems);

        $weeklyOrder = Order::updateOrCreate(
            ['order_type' => 'room', 'status' => 'paid', 'closed_at' => $today->copy()->subDays(6)->addHours(18)],
            [
                'room_session_id' => $sessions->get('Room 3')?->id,
                'user_id' => $supervisor?->id,
                'payment_method' => 'Cash',
                'created_at' => $today->copy()->subDays(6)->addHours(17),
            ]
        );

        $this->syncItems($weeklyOrder, [
            ['name' => 'Pork Sisig', 'qty' => 2],
            ['name' => 'Barkada Combo', 'qty' => 1],
        ], $menuItems);

        $monthlyOrder = Order::updateOrCreate(
            ['order_type' => 'short', 'status' => 'paid', 'closed_at' => $today->copy()->subDays(20)->addHours(16)],
            [
                'user_id' => $cashier?->id,
                'payment_method' => 'Cash',
                'created_at' => $today->copy()->subDays(20)->addHours(14),
            ]
        );

        $this->syncItems($monthlyOrder, [
            ['name' => 'Family Combo', 'qty' => 1],
            ['name' => 'Tapa', 'qty' => 2],
        ], $menuItems);
    }

    /**
     * @param array<int, array{name: string, qty: int}> $items
     */
    private function syncItems(Order $order, array $items, $menuItems): void
    {
        OrderItem::where('order_id', $order->id)->delete();

        foreach ($items as $item) {
            $menuItem = $menuItems->get($item['name']);

            if (!$menuItem) {
                continue;
            }

            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $menuItem->id,
                'name' => $menuItem->name,
                'unit_price' => $menuItem->price,
                'quantity' => $item['qty'],
            ]);
        }
    }
}
