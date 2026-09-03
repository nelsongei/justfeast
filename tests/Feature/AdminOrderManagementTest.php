<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Order;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminOrderManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;
    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name'     => 'Admin Leader',
            'email'    => 'admin@test.com',
            'role'     => 'admin',
            'password' => bcrypt('password'),
        ]);

        $venue = Venue::create([
            'name'     => 'Uhuru Park Arena',
            'location' => 'Nairobi',
        ]);

        $event = Event::create([
            'name'       => 'RheamFeast Live 2026',
            'venue_id'   => $venue->id,
            'start_time' => now(),
            'end_time'   => now()->addHours(6),
            'status'     => 'active',
        ]);

        $vendorUser = User::create([
            'name'     => 'Stall Owner',
            'email'    => 'stall@test.com',
            'role'     => 'vendor',
            'password' => bcrypt('password'),
        ]);

        $vendor = Vendor::create([
            'user_id'       => $vendorUser->id,
            'business_name' => 'Burger World',
            'event_id'      => $event->id,
            'status'        => 'active',
        ]);

        $this->customer = User::create([
            'name'     => 'John Doe',
            'email'    => 'john@test.com',
            'phone'    => '0712345678',
            'role'     => 'customer',
            'password' => bcrypt('password'),
        ]);

        $this->order = Order::create([
            'user_id'         => $this->customer->id,
            'vendor_id'       => $vendor->id,
            'seat_location'   => ['section' => 'VIP A', 'row' => '1', 'seat' => '12'],
            'seat_type'       => 'seat',
            'seat_section'    => 'VIP A',
            'seat_row'        => '1',
            'seat_number'     => '12',
            'total_amount'    => 1030.00,
            'payment_status'  => 'failed',
            'order_status'    => 'created',
        ]);
    }

    public function test_admin_can_update_order_status_and_payment_status(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/admin/orders/{$this->order->id}/status", [
                'order_status'   => 'preparing',
                'payment_status' => 'paid',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        $this->assertDatabaseHas('orders', [
            'id'             => $this->order->id,
            'order_status'   => 'preparing',
            'payment_status' => 'paid',
        ]);
    }

    public function test_admin_can_trigger_stk_push_payment(): void
    {
        Http::fake([
            '*/oauth/v1/generate*' => Http::response([
                'access_token' => 'fake_token_admin_999',
                'expires_in'   => '3599',
            ], 200),
            '*/mpesa/stkpush/v1/processrequest*' => Http::response([
                'MerchantRequestID'   => 'MERCHANT_REQ_ADMIN_01',
                'CheckoutRequestID'   => 'ws_CO_ADMIN_001',
                'ResponseCode'        => '0',
                'ResponseDescription' => 'Success',
                'CustomerMessage'     => 'STK Push sent to phone.',
            ], 200),
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/admin/orders/{$this->order->id}/pay", [
                'phone' => '0712345678',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status'              => 'success',
                'checkout_request_id' => 'ws_CO_ADMIN_001',
                'order_id'            => $this->order->id,
            ]);

        $this->assertDatabaseHas('orders', [
            'id'                        => $this->order->id,
            'mpesa_checkout_request_id' => 'ws_CO_ADMIN_001',
        ]);
    }

    public function test_admin_can_assign_runner_to_order(): void
    {
        $runner = User::create([
            'name'     => 'Runner Alex',
            'email'    => 'runner@test.com',
            'role'     => 'runner',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/admin/orders/{$this->order->id}/status", [
                'runner_id' => $runner->id,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        $this->assertDatabaseHas('orders', [
            'id'           => $this->order->id,
            'runner_id'    => $runner->id,
            'order_status' => 'runner_assigned',
        ]);

        $this->assertDatabaseHas('deliveries', [
            'order_id'  => $this->order->id,
            'runner_id' => $runner->id,
        ]);
    }
}
