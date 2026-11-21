<?php

use App\Models\Subject;
use App\Models\User;

test('example', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

it('allows an admin to create and update subjects', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'fname' => 'Admin',
        'lname' => 'User',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.subjects.store'), [
            'name' => 'Mathematics',
        ])
        ->assertSessionHasNoErrors();

    $subject = Subject::where('name', 'Mathematics')->firstOrFail();

    $this->actingAs($admin)
        ->patch(route('admin.subjects.update', $subject), [
            'name' => 'Advanced Mathematics',
        ])
        ->assertSessionHasNoErrors();

    expect($subject->fresh()->name)->toBe('Advanced Mathematics');
});

it('shows subject class and exam pages to an admin', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'fname' => 'Admin',
        'lname' => 'User',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.subjects.index'))
        ->assertSuccessful();

    $this->actingAs($admin)
        ->get(route('admin.classes.index'))
        ->assertSuccessful();

    $this->actingAs($admin)
        ->get(route('admin.exams.index'))
        ->assertSuccessful();
});

it('allows an admin to create another admin account', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'fname' => 'Primary',
        'lname' => 'Admin',
    ]);

    $response = $this->actingAs($admin)
        ->post(route('admin.users.store'), [
            'fname' => 'Secondary',
            'lname' => 'Admin',
            'email' => 'secondary-admin@example.com',
            'role' => 'admin',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

    $response->assertRedirect()
        ->assertSessionHasNoErrors();

    $createdAdmin = User::where('email', 'secondary-admin@example.com')->firstOrFail();

    expect($createdAdmin->role)->toBe('admin')
        ->and($createdAdmin->student_id)->toBeNull()
        ->and($createdAdmin->teacher_id)->toBeNull();
});

it('allows an admin to create teacher and student accounts', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'fname' => 'Primary',
        'lname' => 'Admin',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.users.store'), [
            'fname' => 'Test',
            'lname' => 'Teacher',
            'email' => 'test-teacher@example.com',
            'role' => 'teacher',
            'account_id' => 'TCH-2001',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->actingAs($admin)
        ->post(route('admin.users.store'), [
            'fname' => 'Test',
            'lname' => 'Student',
            'email' => 'test-student@example.com',
            'role' => 'student',
            'account_id' => 'STU-2001',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    expect(User::where('email', 'test-teacher@example.com')->firstOrFail()->teacher_id)->toBe('TCH-2001')
        ->and(User::where('email', 'test-student@example.com')->firstOrFail()->student_id)->toBe('STU-2001');
});
