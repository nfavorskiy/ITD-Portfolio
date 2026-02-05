<?php

use App\Models\Post;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('users cannot create posts with html in title', function () {
    $this->actingAs($this->user)
        ->post(route('posts.store'), [
            'title' => '<script>alert("XSS")</script>',
            'content' => 'Test content',
        ])
        ->assertSessionHasErrors('title');

    expect(Post::where('user_id', $this->user->id)->exists())->toBeFalse();
});

test('users cannot create posts with link tags in title', function () {
    $this->actingAs($this->user)
        ->post(route('posts.store'), [
            'title' => '<a href="https://evil.com">Click me</a>',
            'content' => 'Test content',
        ])
        ->assertSessionHasErrors('title');

    expect(Post::where('user_id', $this->user->id)->exists())->toBeFalse();
});

test('users can create posts with valid punctuation in title', function () {
    $this->actingAs($this->user)
        ->post(route('posts.store'), [
            'title' => 'My Post: A Journey! What, How?',
            'content' => 'Test content',
        ])
        ->assertRedirect();

    expect(Post::where('title', 'My Post: A Journey! What, How?')->exists())->toBeTrue();
});

test('users cannot update posts with html in title', function () {
    $post = Post::factory()->create(['user_id' => $this->user->id]);
    
    $this->actingAs($this->user)
        ->put(route('posts.update', $post), [
            'title' => '<img src=x onerror="alert(1)">',
            'content' => 'Updated content',
        ])
        ->assertSessionHasErrors('title');

    $post->refresh();
    expect($post->title)->not->toContain('<img');
});

test('title validation rejects special html characters', function () {
    $maliciousInputs = [
        '<script>alert(1)</script>',
        '<iframe src="evil.com"></iframe>',
        '<img src=x onerror=alert(1)>',
        '<svg onload=alert(1)>',
        '<<SCRIPT>alert("XSS");//<</SCRIPT>',
    ];

    foreach ($maliciousInputs as $input) {
        $this->actingAs($this->user)
            ->post(route('posts.store'), [
                'title' => $input,
                'content' => 'Test',
            ])
            ->assertSessionHasErrors('title');
    }
});