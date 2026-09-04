<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderMultiVendorIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected Vendor $vendor1;
    protected Vendor $vendor2;
    protected Product $product1;
    protected Product $product2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::create([
            'name'     => 'Idempotency Tester',
            'email'    => 'tester@test.com',
            'phone'    => '254700000000',
            'role'     => 'customer',
            'password' => bcrypt('password'),
        ]);

        $venue = Venue::create([
            'name'     => 'Test Stadium',
            'location' => 'Nairobi',
        ]);

        $event = Event::create([
            'name'       => 'Idempotency Event',
            'venue_id'   => $venue->id,
            'start_time' => now(),
            'end_time'   => now()->addHours(6),
            'status'     => 'active',
        ]);

        $vUser1 = User::create(['name' => 'V1 Owner', 'email' => 'v1@test.com', 'role' => 'vendor', 'password' => bcrypt('password')]);
        $vUser2 = User::create(['name' => 'V2 Owner', 'email' => 'v2@test.com', 'role' => 'vendor', 'password' => bcrypt('password')]);

        $this->vendor1 = Vendor::create(['user_id' => $vUser1->id, 'business_name' => 'Vendor 1', 'event_id' => $event->id, 'status' => 'active']);
        $this->vendor2 = Vendor::create(['user_id' => $vUser2->id, 'business_name' => 'Vendor 2', 'event_id' => $event->id, 'status' => 'active']);

        $this->product1 = Product::create([
            'vendor_id'      => $this->vendor1->id,
            'name'           => 'Burger 1',
            'price'          => 100,
            'stock_quantity' => 50,
            'stock_status'   => 'in_stock',
        ]);

        $this->product2 = Product::create([
            'vendor_id'      => $this->vendor2->id,
            'name'           => 'Drink 2',
            'price'          => 50,
            'stock_quantity' => 50,
            'stock_status'   => 'in_stock',
        ]);
    }

    public function test_multi_vendor_orders_without_idempotency_key_do_not_trigger_duplicate_key_violation(): void
    {
        Sanctum::actingAs($this->customer);

        $payload = [
            'seat_location' => ['section' => 'VIP A', 'row' => '1', 'seat' => '5'],
            'items'         => [
                ['product_id' => $this->product1->id, 'quantity' => 1],
                ['product_id' => $this->product2->id, 'quantity' => 1],
            ],
        ];

        // Place first multi-vendor order without idempotency key
        $res1 = $this->postJson('/api/orders', $payload);
        $res1->assertStatus(200);

        // Verify idempotency keys for created orders are NULL (not '-v1' or '-v2')
        $orders1 = Order::where('user_id', $this->customer->id)->get();
        $this->assertCount(2, $orders1);
        foreach ($orders1 as $o) {
            $this->assertNull($o->idempotency_key);
        }

        // Place second multi-vendor order without idempotency key (should succeed cleanly without SQL duplicate entry error)
        $res2 = $this->postJson('/api/orders', $payload);
        $res2->assertStatus(200);

        $orders2 = Order::where('user_id', $this->customer->id)->get();
        $this->assertCount(4, $orders2);
    }

    public function test_multi_vendor_orders_with_idempotency_key_are_deduplicated(): void
    {
        Sanctum::actingAs($this->customer);

        $idempotencyKey = 'unique_test_key_999';

        $payload = [
            'seat_location' => ['section' => 'VIP A', 'row' => '1', 'seat' => '5'],
            'items'         => [
                ['product_id' => $this->product1->id, 'quantity' => 1],
                ['product_id' => $this->product2->id, 'quantity' => 1],
            ],
        ];

        $headers = ['Idempotency-Key' => $idempotencyKey];

        // First attempt with idempotency key
        $res1 = $this->postJson('/api/orders', $payload, $headers);
        $res1->assertStatus(200);

        // Second attempt with exact same idempotency key
        $res2 = $this->postJson('/api/orders', $payload, $headers);
        $res2->assertStatus(200)
             ->assertJsonPath('message', 'Order already placed.');
    }
}
