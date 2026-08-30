<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_toggle_vendor_status(): void
    {
        $admin = User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@test.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);

        $venue = Venue::create(['name' => 'Main Venue']);
        $event = Event::create([
            'name'       => 'Test Event',
            'venue_id'   => $venue->id,
            'start_time' => now(),
            'end_time'   => now()->addHours(5),
            'status'     => 'active',
        ]);

        $vendorUser = User::create([
            'name'     => 'Vendor User',
            'email'    => 'vendor@test.com',
            'password' => bcrypt('password'),
            'role'     => 'vendor',
        ]);

        $vendor = Vendor::create([
            'user_id'       => $vendorUser->id,
            'business_name' => 'Coffee Spot',
            'event_id'      => $event->id,
            'status'        => 'active',
        ]);

        // Act: Deactivate vendor account as admin
        $response = $this->actingAs($admin)
            ->patchJson("/api/admin/vendors/{$vendor->id}/status", [
                'status' => 'inactive',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('vendors', [
            'id'     => $vendor->id,
            'status' => 'inactive',
        ]);

        // Act: Reactivate vendor account
        $response = $this->actingAs($admin)
            ->patchJson("/api/admin/vendors/{$vendor->id}/status", [
                'status' => 'active',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('vendors', [
            'id'     => $vendor->id,
            'status' => 'active',
        ]);
    }
}
