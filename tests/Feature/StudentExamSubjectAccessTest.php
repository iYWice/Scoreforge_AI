<?php

use App\Models\ClassSubject;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;

function createStudentExamAccessFixture(): array
{
    $student = User::factory()->create(['role' => 'student']);
    $teacher = User::factory()->create(['role' => 'teacher']);
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
    $exam = Exam::create([
        'title' => 'Restricted Mathematics Exam',
        'exam_code' => 'RESTRICTED1',
        'subject_id' => $subject->id,
        'class_id' => $class->id,
        'total_items' => 1,
        'created_by' => $teacher->id,
        'status' => 'published',
        'duration' => 5,
        'passing_score' => 1,
    ]);

    return compact('student', 'teacher', 'class', 'offering', 'exam');
}

test('class enrollment without subject enrollment does not show or allow a subject exam', function () {
    ['student' => $student, 'class' => $class, 'exam' => $exam] = createStudentExamAccessFixture();
    $class->students()->attach($student->id);
    $attempt = ExamAttempt::create([
        'exam_id' => $exam->id,
        'student_id' => $student->id,
        'status' => 'ongoing',
        'started_at' => now(),
    ]);

    $this->actingAs($student)
        ->get(route('student.classes.show', $class))
        ->assertSuccessful()
        ->assertDontSee($exam->title);

    $this->post(route('student.exam.start'), ['exam_code' => $exam->exam_code])
        ->assertForbidden();

    $this->post(route('student.exam.begin'), [
        'exam_id' => $exam->id,
        'agreement' => '1',
    ])->assertForbidden();

    $this->get(route('student.exam.take', $attempt))->assertForbidden();
    $this->post(route('student.exam.submit', $attempt))->assertForbidden();
    $this->get(route('student.exam.result', $attempt))->assertForbidden();
});

test('a student assigned to the class subject can access its exam', function () {
    ['student' => $student, 'class' => $class, 'offering' => $offering, 'exam' => $exam] = createStudentExamAccessFixture();
    $class->students()->attach($student->id);
    $offering->students()->attach($student->id);

    $this->actingAs($student)
        ->get(route('student.classes.show', $class))
        ->assertSuccessful()
        ->assertSee($exam->title);

    $this->post(route('student.exam.start'), ['exam_code' => $exam->exam_code])
        ->assertSuccessful()
        ->assertSee($exam->title);
});
