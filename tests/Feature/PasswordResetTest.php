<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_request_password_reset()
    {
        User::create([
            'name' => 'Test',
            'email' => 'test@test.com',
            'phone' => '123',
            'role' => 'driver',
            'password' => bcrypt('password'),
        ]);

        $res = $this->postJson('/api/auth/forgot-password', [
            'email' => 'test@test.com'
        ]);

        $res->assertStatus(200);
    }
}
