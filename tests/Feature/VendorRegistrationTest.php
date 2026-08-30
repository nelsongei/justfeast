<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $venue = Venue::create([
            'name' => 'Kasarani Stadium',
        ]);

        Event::create([
            'name'       => 'Summer Fest 2026',
            'venue_id'   => $venue->id,
            'start_time' => now(),
            'end_time'   => now()->addHours(8),
            'status'     => 'active',
        ]);
    }

    public function test_vendor_registration_page_can_be_rendered(): void
    {
        $response = $this->get('/register/vendor');

        $response->assertStatus(200);
        $response->assertSee('Register your Business');
    }

    public function test_vendor_can_register_via_web_form(): void
    {
        $response = $this->post('/register/vendor', [
            'name'                  => 'Chef John',
            'business_name'         => 'Grill Masters',
            'email'                 => 'john@grillmasters.co.ke',
            'phone'                 => '0711223344',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/vendor');
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'john@grillmasters.co.ke',
            'role'  => 'vendor',
        ]);

        $this->assertDatabaseHas('vendors', [
            'business_name' => 'Grill Masters',
            'status'        => 'active',
        ]);
    }

    public function test_vendor_can_register_when_no_events_exist(): void
    {
        Vendor::query()->delete();
        Event::query()->delete();

        $response = $this->post('/register/vendor', [
            'name'                  => 'Fresh Chef',
            'business_name'         => 'Fresh Bites',
            'email'                 => 'fresh@bites.co.ke',
            'phone'                 => '0799887766',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/vendor');
        $this->assertAuthenticated();

        $this->assertDatabaseHas('vendors', [
            'business_name' => 'Fresh Bites',
            'status'        => 'active',
        ]);
    }

    public function test_vendor_registration_requires_business_name(): void
    {
        $response = $this->post('/register/vendor', [
            'name'                  => 'Chef John',
            'business_name'         => '',
            'email'                 => 'john@grillmasters.co.ke',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['business_name']);
        $this->assertDatabaseMissing('users', ['email' => 'john@grillmasters.co.ke']);
    }

    public function test_vendor_can_register_via_api(): void
    {
        $response = $this->postJson('/api/auth/register-vendor', [
            'name'          => 'Jane Doe',
            'business_name' => 'Taco Fiesta',
            'email'         => 'jane@tacofiesta.co.ke',
            'phone'         => '0722334455',
            'password'      => 'secret123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'user' => ['id', 'name', 'email', 'role'],
                'vendor' => ['id', 'business_name', 'status'],
                'token',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'jane@tacofiesta.co.ke',
            'role'  => 'vendor',
        ]);

        $this->assertDatabaseHas('vendors', [
            'business_name' => 'Taco Fiesta',
        ]);
    }
}
