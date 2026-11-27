<?php

use App\Models\ClassSubject;
use App\Models\SchoolClass;
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

it('allows an admin to delete a class with no students subjects or examinations', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $class = SchoolClass::create(['name' => 'Empty class']);

    $this->actingAs($admin)
        ->delete(route('admin.classes.destroy', $class))
        ->assertRedirect()
        ->assertSessionHas('success', 'Class deleted successfully.');

    $this->assertModelMissing($class);
});

it('prevents an admin from deleting a class that has a subject', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $subject = Subject::create(['name' => 'Mathematics']);
    $class = SchoolClass::create([
        'name' => 'Grade 7',
        'subject_id' => $subject->id,
    ]);

    $this->actingAs($admin)
        ->delete(route('admin.classes.destroy', $class))
        ->assertRedirect()
        ->assertSessionHasErrors([
            'class' => 'Class cannot be deleted while it has students, subjects, or examinations.',
        ]);

    $this->assertModelExists($class);
});

it('shows class subject offerings in the admin class list and prevents deletion', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $teacher = User::factory()->create(['role' => 'teacher']);
    $subject = Subject::create(['name' => 'Science']);
    $class = SchoolClass::create([
        'name' => 'Grade 8',
        'created_by' => $teacher->id,
    ]);

    ClassSubject::create([
        'class_id' => $class->id,
        'subject_id' => $subject->id,
        'teacher_id' => $teacher->id,
        'edp_code' => '987654',
        'schedule' => 'Mon | 09:00-10:00',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.classes.index'))
        ->assertSuccessful()
        ->assertSee('Science');

    $this->actingAs($admin)
        ->delete(route('admin.classes.destroy', $class))
        ->assertRedirect()
        ->assertSessionHasErrors('class');

    $this->assertModelExists($class);
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
