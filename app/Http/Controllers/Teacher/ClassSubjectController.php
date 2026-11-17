<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use App\Models\ClassSubject;
use App\Models\User;

class ClassSubjectController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::latest()->get();
        $subjects = Subject::latest()->get();

        return view('teacher.class-subjects.index', compact(
            'classes',
            'subjects'
        ));
    }


    // CREATE CLASS
    public function storeClass(Request $request)
    {
        $validated = $request->validateWithBag(
            'classCreation',
            [
                'name' => 'required|string|max:255',
            ]
        );

        SchoolClass::create([
            'name' => $validated['name'],
                'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('teacher.class-subjects.index')
            ->with('success', 'Class created successfully.');
    }


    // CREATE SUBJECT
    public function storeSubject(Request $request)
    {
        $validated = $request->validateWithBag(
            'subjectCreation',
            [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
            ]
        );

        Subject::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('teacher.class-subjects.index')
            ->with('success', 'Subject created successfully.');
    }


    // DELETE CLASS
    public function destroyClass($id)
    {
        $class = SchoolClass::findOrFail($id);

        if ($class->exams()->exists()) {

            return back()->with(
                'error',
                'This class cannot be deleted because it is currently used by one or more exams.'
            );
        }

        $class->delete();

        return back()->with(
            'success',
            'Class deleted successfully.'
        );
    }


    // DELETE SUBJECT
    public function destroySubject($id)
    {
        $subject = Subject::findOrFail($id);

        if ($subject->exams()->exists()) {

            return back()->with(
                'error',
                'This subject cannot be deleted because it is currently used by one or more exams.'
            );
        }

        $subject->delete();

        return back()->with(
            'success',
            'Subject deleted successfully.'
        );
    }
    // ADD A SUBJECT OFFERING TO A CLASS
public function storeOffering(Request $request, SchoolClass $class)
{
    abort_unless((int) $class->created_by === (int) auth()->id(), 403);

    $validated = $request->validate([
        'subject_name' => ['required', 'string', 'max:255'],
        'edp_code' => ['required', 'string', 'max:50', 'unique:class_subjects,edp_code'],
        'schedule' => ['required', 'string', 'max:255'],
    ]);

    $subject = Subject::firstOrCreate([
        'name' => trim($validated['subject_name']),
    ]);

    $class->classSubjects()->create([
        'subject_id' => $subject->id,
        'teacher_id' => auth()->id(),
        'edp_code' => strtoupper(trim($validated['edp_code'])),
        'schedule' => trim($validated['schedule']),
    ]);

    return back()->with('success', 'Subject offering added to the class.');
}


// ASSIGN JOINED STUDENTS TO A SUBJECT OFFERING
public function assignStudents(Request $request, ClassSubject $classSubject)
{
    $class = $classSubject->schoolClass;

    abort_unless(
        $class && (int) $class->created_by === (int) auth()->id(),
        403
    );

    $validated = $request->validate([
        'student_ids' => ['nullable', 'array'],
        'student_ids.*' => ['integer', 'distinct', 'exists:users,id'],
    ]);

    $studentIds = collect($validated['student_ids'] ?? [])
        ->map(fn ($id) => (int) $id)
        ->unique()
        ->values();

    $eligibleStudentIds = User::query()
        ->where('role', 'student')
        ->whereIn('id', $studentIds)
        ->whereHas('enrolledClasses', function ($query) use ($class) {
            $query->where('classes.id', $class->id);
        })
        ->pluck('id');

    if ($eligibleStudentIds->count() !== $studentIds->count()) {
        return back()->withErrors([
            'student_ids' => 'Select only students who joined this class.',
        ]);
    }

    $syncData = $studentIds->mapWithKeys(fn ($id) => [
        $id => ['assigned_by' => auth()->id()],
    ])->all();

    // This replaces the offering's current student list with the selected list.
    $classSubject->students()->sync($syncData);

    return back()->with('success', 'Students assigned to the subject offering.');
}
}
