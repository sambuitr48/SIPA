<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_request_verification_email()
    {
        $user = User::create([
            'name' => 'A',
            'email' => 'a@a.com',
            'phone' => '1',
            'role' => 'driver',
            'email_verified_at' => null,
            'password' => bcrypt('pass')
        ]);

        $this->actingAs($user);

        $response = $this->postJson('/api/auth/send-verification-email');

        $response->assertStatus(200);
    }
}
