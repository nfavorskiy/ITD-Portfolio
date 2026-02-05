<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationSqlInjectionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_prevents_sql_injection_on_registration()
    {
        $maliciousEmail = "test@example.com'); DROP TABLE users; --";
        $maliciousName = "Hacker'); DROP TABLE posts; --";

        $response = $this->post(route('register'), [
            'name' => $maliciousName,
            'email' => $maliciousEmail,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        // Registration should fail due to invalid input
        $this->assertDatabaseMissing('users', [
            'email' => $maliciousEmail,
        ]);

        // Tables should still exist
        $this->assertTrue(\Schema::hasTable('users'));
        $this->assertTrue(\Schema::hasTable('posts'));
    }
}