<?php

use App\Models\ClassSubject;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
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
        ->assertViewHas('notTakenStudents', function ($notTakenStudents) use ($student) {
            return $notTakenStudents->contains('id', $student->id);
        });
});

it('hides expired published exams from the student class page', function () {
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

    $class->students()->attach($student->id);
    $offering->students()->attach($student->id, ['assigned_by' => $teacher->id]);

    $exam = Exam::create([
        'title' => 'Expired Mathematics Exam',
        'exam_code' => 'EXPIRED1',
        'subject_id' => $subject->id,
        'class_id' => $class->id,
        'total_items' => 1,
        'created_by' => $teacher->id,
        'status' => 'published',
        'duration' => 60,
        'passing_score' => 1,
        'closes_at' => now()->subMinute(),
    ]);

    $this->actingAs($student)
        ->get(route('student.classes.show', $class))
        ->assertSuccessful()
        ->assertDontSee($exam->title);
});

it('closes a published exam when its deadline is reached', function () {
    $teacher = User::factory()->create(['role' => 'teacher']);
    $class = SchoolClass::create([
        'name' => 'Grade 7',
        'created_by' => $teacher->id,
    ]);
    $subject = Subject::create(['name' => 'Mathematics']);

    $exam = Exam::create([
        'title' => 'Deadline Mathematics Exam',
        'exam_code' => 'DEADLINE1',
        'subject_id' => $subject->id,
        'class_id' => $class->id,
        'total_items' => 1,
        'created_by' => $teacher->id,
        'status' => 'published',
        'duration' => 60,
        'passing_score' => 1,
        'closes_at' => now()->subMinute(),
    ]);

    expect($exam->closeIfExpired())->toBeTrue()
        ->and($exam->fresh()->status)->toBe('closed');
});

it('allows a teacher to update and republish an expired exam', function () {
    $teacher = User::factory()->create(['role' => 'teacher']);
    $class = SchoolClass::create([
        'name' => 'Grade 7',
        'created_by' => $teacher->id,
    ]);
    $subject = Subject::create(['name' => 'Mathematics']);

    $exam = Exam::create([
        'title' => 'Expired Exam',
        'exam_code' => 'REOPEN1',
        'subject_id' => $subject->id,
        'class_id' => $class->id,
        'total_items' => 1,
        'created_by' => $teacher->id,
        'status' => 'closed',
        'duration' => 60,
        'passing_score' => 1,
        'opens_at' => now()->subDay(),
        'closes_at' => now()->subHour(),
    ]);

    Question::create([
        'exam_id' => $exam->id,
        'question_type' => 'identification',
        'question_text' => 'What is 2 + 2?',
        'correct_answer' => '4',
        'points' => 1,
    ]);

    $newDeadline = now()->addDay();

    $this->actingAs($teacher)
        ->put(route('teacher.exams.update', $exam), [
            'title' => $exam->title,
            'subject_id' => $subject->id,
            'class_id' => $class->id,
            'total_items' => 1,
            'duration' => 60,
            'opens_at' => now()->subHour()->format('Y-m-d\TH:i'),
            'closes_at' => $newDeadline->format('Y-m-d\TH:i'),
        ])
        ->assertRedirect();

    expect($exam->fresh()->closes_at->format('Y-m-d H:i'))
        ->toBe($newDeadline->format('Y-m-d H:i'));

    $this->actingAs($teacher)
        ->patch(route('teacher.exams.status', $exam), ['status' => 'published'])
        ->assertRedirect();

    expect($exam->fresh()->status)->toBe('published');
});

it('marks an overdue ongoing attempt as timed out', function () {
    $teacher = User::factory()->create(['role' => 'teacher']);
    $student = User::factory()->create(['role' => 'student']);
    $class = SchoolClass::create([
        'name' => 'Grade 7',
        'created_by' => $teacher->id,
    ]);
    $subject = Subject::create(['name' => 'Mathematics']);

    $exam = Exam::create([
        'title' => 'Timed Out Exam',
        'exam_code' => 'TIMEOUT1',
        'subject_id' => $subject->id,
        'class_id' => $class->id,
        'total_items' => 1,
        'created_by' => $teacher->id,
        'status' => 'published',
        'duration' => 30,
        'passing_score' => 1,
    ]);

    $attempt = ExamAttempt::create([
        'exam_id' => $exam->id,
        'student_id' => $student->id,
        'status' => 'ongoing',
        'started_at' => now()->subMinutes(31),
    ]);

    expect($attempt->markTimedOutIfExpired())->toBeTrue()
        ->and($attempt->fresh()->status)->toBe('timed_out');
});

it('defaults to randomized questions and preserves an attempt question order', function () {
    $exam = new Exam([
        'title' => 'Question Order Exam',
        'status' => 'published',
    ]);

    expect($exam->randomize_questions)->toBeTrue();

    $questions = collect([
        new Question(['question_text' => 'One']),
        new Question(['question_text' => 'Two']),
        new Question(['question_text' => 'Three']),
    ]);
    $questions->each(function ($question, $index) {
        $question->id = $index + 1;
    });

    $exam->setRelation('questions', $questions);

    $attempt = new ExamAttempt(['question_order' => [3, 1, 2]]);
    $attempt->setRelation('exam', $exam);

    expect($attempt->orderedQuestions()->pluck('id')->all())
        ->toBe([3, 1, 2]);
});
