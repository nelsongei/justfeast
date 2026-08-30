<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Order;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Venue;
use App\Services\MpesaDarajaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MpesaPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $venue = Venue::create([
            'name'     => 'Kasarani Stadium',
            'location' => 'Nairobi',
        ]);

        $event = Event::create([
            'name'       => 'Concert 2026',
            'venue_id'   => $venue->id,
            'start_time' => now(),
            'end_time'   => now()->addHours(6),
            'status'     => 'active',
        ]);

        $vendorUser = User::create([
            'name'     => 'Vendor One',
            'email'    => 'vendor1@test.com',
            'role'     => 'vendor',
            'password' => bcrypt('password'),
        ]);

        $vendor = Vendor::create([
            'user_id'       => $vendorUser->id,
            'business_name' => 'Feast House',
            'event_id'      => $event->id,
            'status'        => 'active',
        ]);

        $this->customer = User::create([
            'name'     => 'Customer Alice',
            'email'    => 'alice@test.com',
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
            'total_amount'    => 500.00,
            'payment_status'  => 'pending',
            'order_status'    => 'created',
        ]);
    }

    public function test_phone_formatting_utility(): void
    {
        $this->assertEquals('254712345678', MpesaDarajaService::formatPhone('0712345678'));
        $this->assertEquals('254712345678', MpesaDarajaService::formatPhone('+254712345678'));
        $this->assertEquals('254712345678', MpesaDarajaService::formatPhone('254712345678'));
        $this->assertEquals('254112345678', MpesaDarajaService::formatPhone('0112345678'));
    }

    public function test_initiate_stk_push_endpoint(): void
    {
        Http::fake([
            '*/oauth/v1/generate*' => Http::response([
                'access_token' => 'fake_access_token_123',
                'expires_in'   => '3599',
            ], 200),
            '*/mpesa/stkpush/v1/processrequest*' => Http::response([
                'MerchantRequestID'   => 'MERCHANT_REQ_100',
                'CheckoutRequestID'   => 'ws_CO_29082026_001',
                'ResponseCode'        => '0',
                'ResponseDescription' => 'Success. Request accepted for processing',
                'CustomerMessage'     => 'Success. Request accepted for processing',
            ], 200),
        ]);

        $response = $this->actingAs($this->customer, 'sanctum')
            ->postJson("/api/orders/{$this->order->id}/pay", [
                'phone' => '0712345678',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status'              => 'success',
                'checkout_request_id' => 'ws_CO_29082026_001',
                'order_id'            => $this->order->id,
            ]);

        $this->assertDatabaseHas('orders', [
            'id'                        => $this->order->id,
            'mpesa_checkout_request_id' => 'ws_CO_29082026_001',
            'mpesa_merchant_request_id' => 'MERCHANT_REQ_100',
        ]);
    }

    public function test_mpesa_callback_successful_payment(): void
    {
        $this->order->update([
            'mpesa_checkout_request_id' => 'ws_CO_29082026_002',
            'mpesa_merchant_request_id' => 'MERCHANT_REQ_101',
        ]);

        $payload = [
            'Body' => [
                'stkCallback' => [
                    'MerchantRequestID' => 'MERCHANT_REQ_101',
                    'CheckoutRequestID' => 'ws_CO_29082026_002',
                    'ResultCode'        => 0,
                    'ResultDesc'        => 'The service request is processed successfully.',
                    'CallbackMetadata'  => [
                        'Item' => [
                            ['Name' => 'Amount', 'Value' => 500],
                            ['Name' => 'MpesaReceiptNumber', 'Value' => 'QWE1234567'],
                            ['Name' => 'TransactionDate', 'Value' => 20260829100000],
                            ['Name' => 'PhoneNumber', 'Value' => 254712345678],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/mpesa/callback', $payload);

        $response->assertStatus(200)
            ->assertJson(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);

        $this->assertDatabaseHas('orders', [
            'id'                   => $this->order->id,
            'payment_status'       => 'paid',
            'order_status'         => 'accepted',
            'mpesa_receipt_number' => 'QWE1234567',
        ]);
    }

    public function test_mpesa_callback_idempotency_prevents_duplicate_processing(): void
    {
        $this->order->update([
            'mpesa_checkout_request_id' => 'ws_CO_29082026_003',
            'mpesa_merchant_request_id' => 'MERCHANT_REQ_102',
        ]);

        $payload = [
            'Body' => [
                'stkCallback' => [
                    'MerchantRequestID' => 'MERCHANT_REQ_102',
                    'CheckoutRequestID' => 'ws_CO_29082026_003',
                    'ResultCode'        => 0,
                    'ResultDesc'        => 'The service request is processed successfully.',
                    'CallbackMetadata'  => [
                        'Item' => [
                            ['Name' => 'Amount', 'Value' => 500],
                            ['Name' => 'MpesaReceiptNumber', 'Value' => 'QWE1234568'],
                        ],
                    ],
                ],
            ],
        ];

        // Send first webhook request
        $res1 = $this->postJson('/api/mpesa/callback', $payload);
        $res1->assertStatus(200);

        // Send duplicate webhook request
        $res2 = $this->postJson('/api/mpesa/callback', $payload);
        $res2->assertStatus(200);

        $this->assertDatabaseCount('payment_webhook_events', 1);
    }
}
