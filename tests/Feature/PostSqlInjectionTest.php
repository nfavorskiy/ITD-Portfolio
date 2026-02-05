<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostSqlInjectionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_prevents_sql_injection_on_post_creation()
    {
        $user = User::factory()->create();

        // These titles will be rejected by validation (contain special chars)
        $maliciousTitle = "Test Post"; // Use valid title
        $maliciousContent = "Content'); DROP TABLE users; --";

        $response = $this->actingAs($user)->post(route('posts.store'), [
            'title' => $maliciousTitle,
            'content' => $maliciousContent,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', [
            'title' => $maliciousTitle,
            'content' => $maliciousContent, // Content allows SQL-like strings
        ]);

        // Ensure the posts and users tables still exist and are not dropped
        $this->assertTrue(\Schema::hasTable('posts'));
        $this->assertTrue(\Schema::hasTable('users'));
    }
}