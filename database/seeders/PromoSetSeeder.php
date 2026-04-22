<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PromoSetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = \App\Models\MenuCategory::firstOrCreate(
            ['name' => 'Drinks'],
            ['slug' => 'drinks', 'icon' => '🍷']
        );
        $foodCategory = \App\Models\MenuCategory::firstOrCreate(
            ['name' => 'Food'],
            ['slug' => 'food', 'icon' => '🍔']
        );

        $items = [
            'Beers' => ['price' => 80, 'cat' => $category->id],
            'Tower Cocktail' => ['price' => 350, 'cat' => $category->id],
            'Bottle of Alfonso' => ['price' => 800, 'cat' => $category->id],
            'Sizzling Hotdog' => ['price' => 200, 'cat' => $foodCategory->id],
            'Sizzling Sisig' => ['price' => 250, 'cat' => $foodCategory->id],
            'Crispy Bagnet' => ['price' => 300, 'cat' => $foodCategory->id],
            'Kropek' => ['price' => 100, 'cat' => $foodCategory->id],
            'Sizzling Hungarian' => ['price' => 250, 'cat' => $foodCategory->id],
            'French Fries' => ['price' => 150, 'cat' => $foodCategory->id],
            'Chicharon Bulaklak' => ['price' => 250, 'cat' => $foodCategory->id],
            'Cheesy Bacon Fries' => ['price' => 200, 'cat' => $foodCategory->id],
            'Chicken Poppers' => ['price' => 250, 'cat' => $foodCategory->id],
        ];

        $menuItems = [];
        foreach ($items as $name => $data) {
            $menuItems[$name] = \App\Models\MenuItem::firstOrCreate(
                ['name' => $name],
                [
                    'category_id' => $data['cat'],
                    'price' => $data['price'],
                    'is_active' => true,
                    'stock_quantity' => 100,
                ]
            );
        }

        $sets = [
            [
                'name' => 'Set A',
                'price' => 2000,
                'duration_hours' => 2,
                'items' => [
                    'Beers' => 10,
                    'Tower Cocktail' => 1,
                    'Sizzling Hotdog' => 1,
                    'Sizzling Sisig' => 1,
                    'Crispy Bagnet' => 1,
                    'Kropek' => 1,
                ]
            ],
            [
                'name' => 'Set B',
                'price' => 3000,
                'duration_hours' => 3,
                'items' => [
                    'Beers' => 12,
                    'Tower Cocktail' => 1,
                    'Sizzling Hotdog' => 1,
                    'Sizzling Sisig' => 1,
                    'Crispy Bagnet' => 1,
                    'Sizzling Hungarian' => 1,
                    'French Fries' => 1,
                ]
            ],
            [
                'name' => 'Set C',
                'price' => 4000,
                'duration_hours' => 3,
                'items' => [
                    'Beers' => 12,
                    'Tower Cocktail' => 2,
                    'Bottle of Alfonso' => 1,
                    'Chicharon Bulaklak' => 1,
                    'Sizzling Sisig' => 1,
                    'Crispy Bagnet' => 1,
                    'Sizzling Hungarian' => 1,
                ]
            ],
            [
                'name' => 'Set D',
                'price' => 5000,
                'duration_hours' => 5,
                'items' => [
                    'Beers' => 12,
                    'Tower Cocktail' => 2,
                    'Bottle of Alfonso' => 1,
                    'Sizzling Sisig' => 1,
                    'Crispy Bagnet' => 1,
                    'Chicharon Bulaklak' => 1,
                    'Sizzling Hungarian' => 1,
                    'Cheesy Bacon Fries' => 1,
                    'Chicken Poppers' => 1,
                ]
            ],
        ];

        foreach ($sets as $setData) {
            $set = \App\Models\PromoSet::firstOrCreate(
                ['name' => $setData['name']],
                [
                    'price' => $setData['price'],
                    'duration_hours' => $setData['duration_hours'],
                    'is_active' => true,
                ]
            );

            foreach ($setData['items'] as $itemName => $qty) {
                \App\Models\PromoSetItem::firstOrCreate([
                    'promo_set_id' => $set->id,
                    'menu_item_id' => $menuItems[$itemName]->id,
                ], [
                    'quantity' => $qty,
                ]);
            }
        }
    }
}
