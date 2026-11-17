<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use App\Models\User;

class ClassController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::where('created_by', auth()->id())
            ->with('subject')
            ->withCount('students')
            ->latest()
            ->get();

        $subjects = Subject::orderBy('name')->get();

        return view('teacher.classes.index', compact(
            'classes'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        SchoolClass::create([
            'name' => $validated['name'],
            'subject_id' => null,
            'created_by' => auth()->id(),
            'class_code' => SchoolClass::generateClassCode(),
        ]);

        return redirect()
            ->route('teacher.classes.index')
            ->with('success', 'Class created successfully.');
    }

    public function show(SchoolClass $class)
    {
        abort_if(
            $class->created_by !== auth()->id(),
            403
        );

$class->load([
    'subject',
    'students',
    'classSubjects.subject',
    'classSubjects.students',
    'exams' => fn ($query) => $query->latest(),
]);

        return view(
            'teacher.classes.show',
            compact('class')
        );
    }

    public function update(
        Request $request,
        SchoolClass $class
    ) {
        abort_if(
            $class->created_by !== auth()->id(),
            403
        );

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subject_id' => ['required', 'exists:subjects,id'],
        ]);

        $class->update($validated);

        return back()->with(
            'success',
            'Class updated successfully.'
        );
    }
    public function removeStudent(SchoolClass $class, User $student)
{
    abort_unless(
        (int) $class->created_by === (int) auth()->id(),
        403
    );

    abort_unless($student->role === 'student', 404);

    $isEnrolled = $class->students()
        ->where('users.id', $student->id)
        ->exists();

    abort_unless($isEnrolled, 404);

    // Remove this student from every subject offering in this class.
    foreach ($class->classSubjects()->get() as $offering) {
        $offering->students()->detach($student->id);
    }

    // Remove the class enrollment. Exam attempts are preserved.
    $class->students()->detach($student->id);

    app(\App\Services\ActivityLogger::class)->log(
        'class.student_removed',
        'Teacher removed a student from a class.',
        $class,
        ['student_id' => $student->id]
    );

    return back()->with('success', 'Student removed from the class.');
}
}
