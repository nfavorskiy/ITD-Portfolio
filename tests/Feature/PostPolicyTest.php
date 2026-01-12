<?php

use App\Models\Post;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->otherUser = User::factory()->create();
});

test('guest is redirected from posts list', function () {
    $this->get(route('posts.index'))->assertRedirect(route('login'));
});

test('authenticated user can view posts list', function () {
    $this->actingAs($this->user)
        ->get(route('posts.index'))
        ->assertOk();
});

test('guest is redirected from viewing a post', function () {
    $post = Post::factory()->create(['user_id' => $this->user->id]);
    
    $this->get(route('posts.show', $post))->assertRedirect(route('login'));
});

test('authenticated user can view a post', function () {
    $post = Post::factory()->create(['user_id' => $this->user->id]);
    
    $this->actingAs($this->user)
        ->get(route('posts.show', $post))
        ->assertOk();
});

test('authenticated user can access create post form', function () {
    $this->actingAs($this->user)
        ->get(route('posts.create'))
        ->assertOk();
});

test('guest cannot create posts', function () {
    $this->get(route('posts.create'))
        ->assertRedirect(route('login'));
});

test('authenticated user can store a post', function () {
    $this->actingAs($this->user)
        ->post(route('posts.store'), [
            'title' => 'Test Post',
            'content' => 'Test content for the post.',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('posts', [
        'title' => 'Test Post',
        'user_id' => $this->user->id,
    ]);
});

test('user can edit own post', function () {
    $post = Post::factory()->create(['user_id' => $this->user->id]);
    
    $this->actingAs($this->user)
        ->get(route('posts.edit', $post))
        ->assertOk();
});

test('user cannot edit others post', function () {
    $post = Post::factory()->create(['user_id' => $this->otherUser->id]);
    
    $this->actingAs($this->user)
        ->get(route('posts.edit', $post))
        ->assertForbidden();
});

test('admin cannot edit others post', function () {
    $post = Post::factory()->create(['user_id' => $this->user->id]);
    
    $this->actingAs($this->admin)
        ->get(route('posts.edit', $post))
        ->assertForbidden();
});

test('user can update own post', function () {
    $post = Post::factory()->create(['user_id' => $this->user->id]);
    
    $this->actingAs($this->user)
        ->put(route('posts.update', $post), [
            'title' => 'Updated Title',
            'content' => 'Updated content.',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'title' => 'Updated Title',
    ]);
});

test('user cannot update others post', function () {
    $post = Post::factory()->create(['user_id' => $this->otherUser->id]);
    
    $this->actingAs($this->user)
        ->put(route('posts.update', $post), [
            'title' => 'Hacked Title',
            'content' => 'Hacked content.',
        ])
        ->assertForbidden();
});

test('user can delete own post', function () {
    $post = Post::factory()->create(['user_id' => $this->user->id]);
    
    $this->actingAs($this->user)
        ->delete(route('posts.destroy', $post))
        ->assertRedirect(route('posts.index'));
    
    $this->assertDatabaseMissing('posts', ['id' => $post->id]);
});

test('user cannot delete others post', function () {
    $post = Post::factory()->create(['user_id' => $this->otherUser->id]);
    
    $this->actingAs($this->user)
        ->delete(route('posts.destroy', $post))
        ->assertForbidden();
});

test('admin can delete any post', function () {
    $post = Post::factory()->create(['user_id' => $this->user->id]);
    
    $this->actingAs($this->admin)
        ->delete(route('posts.destroy', $post))
        ->assertRedirect(route('posts.index'));
    
    $this->assertDatabaseMissing('posts', ['id' => $post->id]);
});