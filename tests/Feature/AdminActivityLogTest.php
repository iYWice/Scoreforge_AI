<?php

use App\Models\ActivityLog;
use App\Models\User;

it('shows account ids in admin user management', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'fname' => 'Admin',
        'lname' => 'User',
    ]);

    $student = User::factory()->create([
        'role' => 'student',
        'fname' => 'Student',
        'lname' => 'One',
        'student_id' => 'STU-1001',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertSuccessful()
        ->assertSee('STU-1001');
});

it('exports activity logs without ip addresses', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'fname' => 'Admin',
        'lname' => 'User',
    ]);

    ActivityLog::create([
        'user_id' => $admin->id,
        'action' => 'test.action',
        'description' => 'Test activity',
        'ip_address' => '203.0.113.10',
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.activity-logs.export'));

    $response->assertSuccessful();

    ob_start();
    $response->sendContent();
    $csv = ob_get_clean();

    expect($csv)->toContain('Log ID')
        ->not->toContain('IP Address')
        ->not->toContain('203.0.113.10');
});
