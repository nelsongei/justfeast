<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Order;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Venue;
use App\Models\Delivery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RunnerClaimOrderTest extends TestCase
{
    use RefreshDatabase;

    protected User $runner;
    protected User $otherRunner;
    protected User $customer;
    protected Vendor $vendor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->runner = User::create([
            'name'     => 'Fast Runner',
            'email'    => 'runner1@test.com',
            'phone'    => '254711111111',
            'role'     => 'runner',
            'password' => bcrypt('password'),
        ]);

        $this->otherRunner = User::create([
            'name'     => 'Second Runner',
            'email'    => 'runner2@test.com',
            'phone'    => '254722222222',
            'role'     => 'runner',
            'password' => bcrypt('password'),
        ]);

        $venue = Venue::create([
            'name'     => 'Rhema Stadium',
            'location' => 'Nairobi',
        ]);

        $event = Event::create([
            'name'       => 'Rhema Feast 2026',
            'venue_id'   => $venue->id,
            'start_time' => now(),
            'end_time'   => now()->addHours(6),
            'status'     => 'active',
        ]);

        $vendorUser = User::create([
            'name'     => 'Grill Master',
            'email'    => 'vendor@test.com',
            'role'     => 'vendor',
            'password' => bcrypt('password'),
        ]);

        $this->vendor = Vendor::create([
            'user_id'       => $vendorUser->id,
            'business_name' => 'BBQ Grill',
            'event_id'      => $event->id,
            'status'        => 'active',
        ]);

        $this->customer = User::create([
            'name'     => 'John Customer',
            'email'    => 'customer@test.com',
            'role'     => 'customer',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_runner_can_view_available_unassigned_orders(): void
    {
        Sanctum::actingAs($this->runner);

        // Unassigned available order
        $unassignedOrder = Order::create([
            'user_id'        => $this->customer->id,
            'vendor_id'      => $this->vendor->id,
            'runner_id'      => null,
            'seat_location'  => ['section' => 'VIP A', 'row' => '2', 'seat' => '10'],
            'total_amount'   => 1500,
            'payment_status' => 'paid',
            'order_status'   => 'preparing',
        ]);

        // Already assigned order
        $assignedOrder = Order::create([
            'user_id'        => $this->customer->id,
            'vendor_id'      => $this->vendor->id,
            'runner_id'      => $this->otherRunner->id,
            'seat_location'  => ['section' => 'GEN B', 'row' => '5', 'seat' => '22'],
            'total_amount'   => 800,
            'payment_status' => 'paid',
            'order_status'   => 'runner_assigned',
        ]);

        $response = $this->getJson('/api/runner/available-orders');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(1, 'orders')
            ->assertJsonPath('orders.0.id', $unassignedOrder->id);
    }

    public function test_runner_can_claim_unassigned_order(): void
    {
        Sanctum::actingAs($this->runner);

        $order = Order::create([
            'user_id'        => $this->customer->id,
            'vendor_id'      => $this->vendor->id,
            'runner_id'      => null,
            'seat_location'  => ['section' => 'VIP A', 'row' => '2', 'seat' => '10'],
            'total_amount'   => 1500,
            'payment_status' => 'paid',
            'order_status'   => 'ready',
        ]);

        $response = $this->postJson("/api/runner/orders/{$order->id}/claim");

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('order.runner_id', $this->runner->id)
            ->assertJsonPath('order.order_status', 'runner_assigned');

        $this->assertDatabaseHas('orders', [
            'id'           => $order->id,
            'runner_id'    => $this->runner->id,
            'order_status' => 'runner_assigned',
        ]);

        $this->assertDatabaseHas('deliveries', [
            'order_id'  => $order->id,
            'runner_id' => $this->runner->id,
            'status'    => 'pending',
        ]);
    }

    public function test_runner_cannot_claim_already_claimed_order(): void
    {
        Sanctum::actingAs($this->runner);

        $order = Order::create([
            'user_id'        => $this->customer->id,
            'vendor_id'      => $this->vendor->id,
            'runner_id'      => $this->otherRunner->id,
            'seat_location'  => ['section' => 'VIP A', 'row' => '2', 'seat' => '10'],
            'total_amount'   => 1500,
            'payment_status' => 'paid',
            'order_status'   => 'runner_assigned',
        ]);

        $response = $this->postJson("/api/runner/orders/{$order->id}/claim");

        $response->assertStatus(422)
            ->assertJsonPath('status', 'error')
            ->assertJsonPath('message', 'This order has already been claimed by another runner.');
    }
}
