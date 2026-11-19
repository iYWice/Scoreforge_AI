<?php

use App\Models\ClassSubject;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;

function createTeacherClass(User $teacher, string $name): SchoolClass
{
    return SchoolClass::create([
        'name' => $name,
        'created_by' => $teacher->id,
    ]);
}

function enrollStudent(SchoolClass $class, User $student): void
{
    $class->students()->attach($student->id);
}

function validSubjectOfferingPayload(SchoolClass $class, array $studentIds): array
{
    return [
        'subject_name' => 'Mathematics',
        'edp_code' => '123456',
        'class_id' => $class->id,
        'schedule_days' => ['Mon'],
        'start_time' => '09:00',
        'end_time' => '10:00',
        'student_ids' => $studentIds,
    ];
}

test('subject student choices include class membership for filtering', function () {
    $teacher = User::factory()->create(['role' => 'teacher']);
    $class = createTeacherClass($teacher, 'Class A');
    $student = User::factory()->create(['role' => 'student']);
    enrollStudent($class, $student);

    $response = $this->actingAs($teacher)->get(route('teacher.subjects.index'));

    $response->assertSuccessful();
    $response->assertSee('data-class-ids="'.$class->id.'"', false);
});

test('teacher can enroll only students from the selected class in a subject', function () {
    $teacher = User::factory()->create(['role' => 'teacher']);
    $selectedClass = createTeacherClass($teacher, 'Class A');
    $otherClass = createTeacherClass($teacher, 'Class B');
    $selectedStudent = User::factory()->create(['role' => 'student']);
    $otherStudent = User::factory()->create(['role' => 'student']);
    enrollStudent($selectedClass, $selectedStudent);
    enrollStudent($otherClass, $otherStudent);

    $response = $this->actingAs($teacher)->post(
        route('teacher.subjects.store'),
        validSubjectOfferingPayload($selectedClass, [$selectedStudent->id, $otherStudent->id])
    );

    $response->assertSessionHasErrors('student_ids');
    $this->assertDatabaseMissing('class_subjects', ['edp_code' => '123456']);
});

test('teacher can update a subject roster only with students from its selected class', function () {
    $teacher = User::factory()->create(['role' => 'teacher']);
    $selectedClass = createTeacherClass($teacher, 'Class A');
    $otherClass = createTeacherClass($teacher, 'Class B');
    $otherStudent = User::factory()->create(['role' => 'student']);
    enrollStudent($otherClass, $otherStudent);
    $subject = Subject::create(['name' => 'Mathematics']);
    $offering = ClassSubject::create([
        'class_id' => $selectedClass->id,
        'subject_id' => $subject->id,
        'teacher_id' => $teacher->id,
        'edp_code' => '654321',
        'schedule' => 'Mon | 09:00-10:00',
    ]);

    $response = $this->actingAs($teacher)->post(
        route('teacher.subjects.students.sync', $offering),
        [
            'class_id' => $selectedClass->id,
            'student_ids' => [$otherStudent->id],
        ]
    );

    $response->assertSessionHasErrors('student_ids');
    $this->assertDatabaseMissing('subject_enrollments', [
        'class_subject_id' => $offering->id,
        'student_id' => $otherStudent->id,
    ]);
});
