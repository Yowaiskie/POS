<?php

namespace Database\Seeders;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Bowl Meal', 'icon' => '🍜'],
            ['name' => 'Silog Meal', 'icon' => '🍳'],
            ['name' => 'Sizzling Plate', 'icon' => '🔥'],
            ['name' => 'Combo Meal', 'icon' => '🍱'],
        ];

        foreach ($categories as $category) {
            MenuCategory::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                ['name' => $category['name'], 'icon' => $category['icon']]
            );
        }

        $items = [
            'Bowl Meal' => [
                ['name' => 'Cheesy Katsu', 'price' => 89],
                ['name' => 'Cheesy Karaage', 'price' => 89],
                ['name' => 'Mushroom Gravy', 'price' => 89],
                ['name' => 'Creamy Salted Egg Chicken', 'price' => 105],
                ['name' => 'Orange Chicken', 'price' => 89],
                ['name' => 'Buffalo Pops', 'price' => 89],
                ['name' => 'Sisig Popcorn Chicken', 'price' => 89],
                ['name' => 'Chicken Skin', 'price' => 89],
            ],
            'Silog Meal' => [
                ['name' => 'Hungarian', 'price' => 105],
                ['name' => 'Tapa', 'price' => 115],
                ['name' => 'Longganisa', 'price' => 99],
                ['name' => 'Tocino', 'price' => 99],
            ],
            'Sizzling Plate' => [
                ['name' => 'Pork Sisig', 'price' => 139],
                ['name' => 'Chicken Inasal', 'price' => 129],
                ['name' => 'Beef Tapa', 'price' => 149],
            ],
            'Combo Meal' => [
                ['name' => 'Barkada Combo', 'price' => 399],
                ['name' => 'Family Combo', 'price' => 599],
                ['name' => 'Party Combo', 'price' => 799],
            ],
        ];

        foreach ($items as $categoryName => $categoryItems) {
            $category = MenuCategory::where('name', $categoryName)->first();

            if (!$category) {
                continue;
            }

            foreach ($categoryItems as $item) {
                MenuItem::updateOrCreate(
                    ['name' => $item['name'], 'category_id' => $category->id],
                    ['price' => $item['price'], 'is_active' => true]
                );
            }
        }
    }
}
