<?php

use App\Models\ClassSubject;
use App\Models\Exam;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Carbon;

it('is unavailable before opens_at and after closes_at', function () {
    $exam = new Exam([
        'status' => 'published',
        'opens_at' => now()->addHour(),
        'closes_at' => now()->addDay(),
    ]);

    expect($exam->isWithinAvailabilityWindow())->toBeFalse()
        ->and($exam->availabilityMessage())->toContain('opens at')
        ->and($exam->availabilityMessage())->toContain('Philippine Time');

    Carbon::setTestNow(now()->addDays(2));

    expect($exam->isWithinAvailabilityWindow())->toBeFalse();

    Carbon::setTestNow();
});

it('is permanently available when no deadline is set', function () {
    $exam = new Exam([
        'status' => 'published',
        'opens_at' => null,
        'closes_at' => null,
    ]);

    expect($exam->isWithinAvailabilityWindow())->toBeTrue();
});

it('is unavailable unless the exam is published', function () {
    $exam = new Exam([
        'status' => 'draft',
        'opens_at' => null,
        'closes_at' => null,
    ]);

    expect($exam->isWithinAvailabilityWindow())->toBeFalse();
});

it('limits an attempt to the earliest duration or scheduled deadline', function () {
    Carbon::setTestNow(Carbon::parse('2026-11-21 15:00:00', 'Asia/Manila'));

    $exam = new Exam([
        'status' => 'published',
        'duration' => 60,
        'opens_at' => Carbon::parse('2026-11-21 13:00:00', 'Asia/Manila'),
        'closes_at' => Carbon::parse('2026-11-21 23:59:00', 'Asia/Manila'),
    ]);

    expect($exam->effectiveDeadline(now())->format('Y-m-d H:i:s'))
        ->toBe('2026-11-21 16:00:00');

    $exam->closes_at = Carbon::parse('2026-11-21 15:30:00', 'Asia/Manila');

    expect($exam->effectiveDeadline(now())->format('Y-m-d H:i:s'))
        ->toBe('2026-11-21 15:30:00');

    Carbon::setTestNow();
});

it('renders the teacher exam response page', function () {
    $teacher = User::factory()->create(['role' => 'teacher']);
    $student = User::factory()->create(['role' => 'student']);
    $class = SchoolClass::create([
        'name' => 'Grade 7',
        'created_by' => $teacher->id,
    ]);
    $subject = Subject::create(['name' => 'Mathematics']);
    $offering = ClassSubject::create([
        'class_id' => $class->id,
        'subject_id' => $subject->id,
        'teacher_id' => $teacher->id,
        'edp_code' => '789012',
        'schedule' => 'Mon | 09:00-10:00',
    ]);
    $offering->students()->attach($student->id, ['assigned_by' => $teacher->id]);

    $exam = Exam::create([
        'title' => 'Scheduled Mathematics Exam',
        'exam_code' => 'SCHEDULED1',
        'subject_id' => $subject->id,
        'class_id' => $class->id,
        'total_items' => 1,
        'created_by' => $teacher->id,
        'status' => 'published',
        'duration' => 60,
        'passing_score' => 1,
    ]);

    $this->actingAs($teacher)
        ->get(route('teacher.exams.show', $exam))
        ->assertSuccessful()
        ->assertViewHas('notTakenStudents');
});
