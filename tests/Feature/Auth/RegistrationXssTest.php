<?php

use App\Models\User;

test('users cannot register with html in username', function () {
    $response = $this->post('/register', [
        'name' => '<a href="https://evil.com">Click me</a>',
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertSessionHasErrors('name');
    $this->assertGuest();
    expect(User::where('email', 'test@example.com')->exists())->toBeFalse();
});

test('users cannot register with script tags in username', function () {
    $response = $this->post('/register', [
        'name' => '<script>alert("XSS")</script>',
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertSessionHasErrors('name');
    $this->assertGuest();
});

test('users can register with valid alphanumeric usernames', function () {
    $response = $this->post('/register', [
        'name' => 'John_Doe-123',
        'email' => 'john@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertRedirect(route('verification.notice'));
    expect(User::where('name', 'John_Doe-123')->exists())->toBeTrue();
});