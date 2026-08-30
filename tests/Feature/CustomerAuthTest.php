<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_mode_returns_404_when_account_is_not_registered()
    {
        $response = $this->postJson('/api/auth/login', [
            'phone' => '0712345678',
            'mode'  => 'login',
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'code'   => 'ACCOUNT_NOT_FOUND',
            ]);
    }

    public function test_login_mode_succeeds_when_account_is_registered()
    {
        User::factory()->create([
            'phone' => '0712345678',
            'role'  => 'customer',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'phone' => '0712345678',
            'mode'  => 'login',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status'      => 'success',
                'is_existing' => true,
            ]);
    }

    public function test_register_mode_allows_new_user_otp_and_verification()
    {
        $loginRes = $this->postJson('/api/auth/login', [
            'phone' => '0799887766',
            'name'  => 'Jane Doe',
            'mode'  => 'register',
        ]);

        $loginRes->assertStatus(200);
        $otp = $loginRes->json('otp');

        $verifyRes = $this->postJson('/api/auth/verify', [
            'phone' => '0799887766',
            'code'  => $otp,
            'name'  => 'Jane Doe',
        ]);

        $verifyRes->assertStatus(200)
            ->assertJsonPath('user.name', 'Jane Doe')
            ->assertJsonPath('user.role', 'customer');

        $this->assertDatabaseHas('users', [
            'phone' => '0799887766',
            'name'  => 'Jane Doe',
        ]);
    }
}
