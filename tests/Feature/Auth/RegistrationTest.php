<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'fname' => 'Test',
        'lname' => 'User',
        'email' => 'test@example.com',
        'role' => 'student',
        'account_id' => '123456',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('registration account IDs accept numbers only', function () {
    $response = $this->from('/')->post('/register', [
        'fname' => 'Test',
        'lname' => 'User',
        'email' => 'numeric-id@example.com',
        'role' => 'student',
        'account_id' => 'ABC123',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors('account_id');
    $this->assertGuest();
});

test('registration shows validation errors clearly', function () {
    $response = $this->from('/')->post('/register', [
        'fname' => '',
        'lname' => '',
        'email' => 'not-an-email',
        'role' => 'student',
        'account_id' => '123456',
        'password' => 'short',
        'password_confirmation' => 'different',
    ]);

    $response->assertSessionHasErrors([
        'fname',
        'lname',
        'email',
        'password',
    ]);
    $this->assertGuest();
});

test('registration with valid data shows no errors', function () {
    $response = $this->from('/')->post('/register', [
        'fname' => 'Valid',
        'lname' => 'User',
        'email' => 'valid@example.com',
        'role' => 'student',
        'account_id' => '654321',
        'password' => 'validpassword',
        'password_confirmation' => 'validpassword',
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});
