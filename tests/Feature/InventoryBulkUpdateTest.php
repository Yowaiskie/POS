<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\User;
use App\Models\MenuCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryBulkUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    public function test_admin_can_bulk_update_inventory()
    {
        $admin = User::factory()->create(['position' => 'Admin']);
        $category = MenuCategory::create(['name' => 'Drinks', 'slug' => 'drinks']);
        
        $item1 = MenuItem::create([
            'category_id' => $category->id,
            'name' => 'Beer',
            'price' => 50,
            'stock_quantity' => 10,
        ]);
        
        $item2 = MenuItem::create([
            'category_id' => $category->id,
            'name' => 'Coke',
            'price' => 30,
            'stock_quantity' => 5,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('inventory.bulk-update'), [
                'items' => [
                    $item1->id => [
                        'stock_quantity' => 20,
                    ],
                    $item2->id => [
                        'unlimited' => '1',
                    ],
                ]
            ]);

        $response->assertStatus(302);
        $this->assertEquals(20, $item1->fresh()->stock_quantity);
        $this->assertNull($item2->fresh()->stock_quantity);
    }

    public function test_non_admin_cannot_bulk_update_inventory()
    {
        $user = User::factory()->create(['position' => 'Staff']);
        
        $response = $this->actingAs($user)
            ->post(route('inventory.bulk-update'), [
                'items' => []
            ]);

        $response->assertStatus(403);
    }
}
