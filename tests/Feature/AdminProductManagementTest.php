<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Vendor $vendor;

    protected function setUp(): void
    {
        parent::setUp();

        $venue = Venue::create(['name' => 'Main Stadium']);
        $event = Event::create([
            'name'       => 'Concert 2026',
            'venue_id'   => $venue->id,
            'start_time' => now(),
            'end_time'   => now()->addHours(6),
            'status'     => 'active',
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $vendorUser = User::factory()->create(['role' => 'vendor']);
        $this->vendor = Vendor::create([
            'user_id'       => $vendorUser->id,
            'event_id'      => $event->id,
            'business_name' => 'Samosa Corner',
            'status'        => 'active',
        ]);
    }

    public function test_admin_can_update_vendor_product_via_api(): void
    {
        $product = Product::create([
            'vendor_id'    => $this->vendor->id,
            'name'         => 'Chicken Samosa',
            'description'  => 'Old description',
            'price'        => 50.00,
            'stock_status' => 'in_stock',
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/admin/products/{$product->id}", [
                '_method'      => 'PUT',
                'name'         => 'Chicken Samosa Special',
                'price'        => 65.00,
                'description'  => 'Updated chicken samosa with special sauce',
                'stock_status' => 'in_stock',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('products', [
            'id'          => $product->id,
            'name'        => 'Chicken Samosa Special',
            'price'       => 65.00,
            'description' => 'Updated chicken samosa with special sauce',
        ]);
    }
}
