<?php

/*
Test Case ID: AIQ-CASE-001
Module Name: Authentication Module
Unit Name: Login
*/

it('rejects an invalid email format on login', function () {
    $response = $this->from('/login')->post('/login', [
        'email' => 'not-an-email',
        'password' => 'ExamplePass123',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});
