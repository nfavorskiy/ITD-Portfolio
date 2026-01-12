<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationSqlInjectionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_prevents_sql_injection_on_registration()
    {
        $maliciousEmail = "test@example.com'); DROP TABLE users; --";
        $maliciousName = "Hacker'); DROP TABLE posts; --";

        $response = $this->post(route('register'), [
            'name' => $maliciousName,
            'email' => $maliciousEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Registration should fail due to invalid email, but not SQL injection
        $this->assertDatabaseMissing('users', [
            'email' => $maliciousEmail,
        ]);

        $this->assertTrue(\Schema::hasTable('users'));
        $this->assertTrue(\Schema::hasTable('posts'));
    }
}