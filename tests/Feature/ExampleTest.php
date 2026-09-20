<?php

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;

it('returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('take exam view receives the remaining seconds countdown value', function () {
    $student = User::factory()->create([
        'role' => 'student',
    ]);

    $teacher = User::factory()->create([
        'role' => 'teacher',
    ]);

    $subject = Subject::create([
        'name' => 'Mathematics',
        'description' => 'Math subject',
    ]);

    $class = SchoolClass::create([
        'name' => 'Grade 7',
        'school_year' => '2026',
    ]);

    $exam = Exam::create([
        'title' => 'Sample Exam',
        'exam_code' => 'SAMPLE2026',
        'subject_id' => $subject->id,
        'class_id' => $class->id,
        'total_items' => 1,
        'created_by' => $teacher->id,
        'status' => 'published',
        'duration' => 5,
        'passing_score' => 1,
    ]);

    $attempt = ExamAttempt::create([
        'exam_id' => $exam->id,
        'student_id' => $student->id,
        'status' => 'ongoing',
        'started_at' => now(),
    ]);

    $response = $this
        ->actingAs($student)
        ->get('/student/exam/' . $attempt->id . '/take');

    $response->assertOk();
    $response->assertViewHas('remainingSeconds');
});
