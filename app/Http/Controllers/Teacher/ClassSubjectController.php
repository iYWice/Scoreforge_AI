<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassSubject;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClassSubjectController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::where('created_by', auth()->id())
            ->latest()
            ->get();
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
            'edp_code' => ['required', 'digits:6', 'unique:class_subjects,edp_code'],
            'schedule_days' => ['required', 'array', 'min:1'],
            'schedule_days.*' => ['required', 'string', 'in:Mon,Tue,Wed,Thu,Fri,Sat,Sun'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'room' => ['nullable', 'string', 'max:255'],
        ]);

        $subject = Subject::firstOrCreate([
            'name' => trim($validated['subject_name']),
        ]);

        $schedule = implode(', ', $validated['schedule_days'])
            .' | '.$validated['start_time'].'-'.$validated['end_time'];

        if (! empty($validated['room'])) {
            $schedule .= ' | '.$validated['room'];
        }

        $class->classSubjects()->create([
            'subject_id' => $subject->id,
            'teacher_id' => auth()->id(),
            'edp_code' => strtoupper(trim($validated['edp_code'])),
            'schedule' => $schedule,
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

        $eligibleStudentIds = $class->students()
            ->where('users.role', 'student')
            ->whereIn('users.id', $studentIds)
            ->pluck('users.id')
            ->map(fn ($id) => (int) $id);

        if ($eligibleStudentIds->count() !== $studentIds->count()) {
            return back()->withErrors([
                'student_ids' => 'Select students enrolled in the selected class only.',
            ]);
        }

        $syncData = $studentIds->mapWithKeys(fn ($id) => [
            $id => ['assigned_by' => auth()->id()],
        ])->all();

        // This replaces the offering's current student list with the selected list.
        $classSubject->students()->sync($syncData);

        return back()->with('success', 'Students assigned to the subject offering.');
    }

    public function teacherSubjects()
{
    $offerings = ClassSubject::query()
        ->where('teacher_id', auth()->id())
        ->with(['subject', 'schoolClass.students', 'students'])
        ->withCount('students')
        ->latest()
        ->get();

    $classes = SchoolClass::query()
        ->where('created_by', auth()->id())
        ->with('students')
        ->orderBy('name')
        ->get();

    $students = User::query()
        ->where('role', 'student')
        ->orderBy('lname')
        ->orderBy('fname')
        ->get(['id', 'fname', 'lname', 'email']);

    return view('teacher.subjects.index', compact(
        'offerings',
        'classes',
        'students'
    ));
}

    public function storeIndependentSubject(Request $request)
    {
        $request->merge([
            'edp_code' => strtoupper(trim((string) $request->input('edp_code'))),
        ]);

        $validated = $request->validate([
            'subject_name' => ['required', 'string', 'max:255'],
            'edp_code' => ['required', 'digits:6', 'unique:class_subjects,edp_code'],
            'class_id' => [
                'required',
                Rule::exists('classes', 'id')
                    ->where('created_by', auth()->id()),
            ],
            'schedule_days' => ['required', 'array', 'min:1'],
            'schedule_days.*' => ['required', 'string', 'in:Mon,Tue,Wed,Thu,Fri,Sat,Sun'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'room' => ['nullable', 'string', 'max:255'],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['integer', 'distinct', 'exists:users,id'],
        ]);

        $class = SchoolClass::query()
            ->whereKey($validated['class_id'])
            ->where('created_by', auth()->id())
            ->firstOrFail();

        $studentIds = collect($validated['student_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $eligibleStudentIds = $class->students()
            ->where('users.role', 'student')
            ->whereIn('users.id', $studentIds)
            ->pluck('users.id')
            ->map(fn ($id) => (int) $id);

        if ($eligibleStudentIds->count() !== $studentIds->count()) {
            return back()
                ->withInput()
                ->withErrors(['student_ids' => 'Select students enrolled in the selected class only.']);
        }

        $schedule = implode(', ', $validated['schedule_days'])
            .' | '.$validated['start_time'].'-'.$validated['end_time'];

        if (! empty($validated['room'])) {
            $schedule .= ' | '.$validated['room'];
        }

        DB::transaction(function () use ($validated, $class, $studentIds, $schedule) {
            $subject = Subject::firstOrCreate([
                'name' => trim($validated['subject_name']),
            ]);

            $offering = ClassSubject::create([
                'class_id' => $class->id,
                'subject_id' => $subject->id,
                'teacher_id' => auth()->id(),
                'edp_code' => $validated['edp_code'],
                'schedule' => $schedule,
            ]);

            $syncData = $studentIds->mapWithKeys(fn ($id) => [
                $id => ['assigned_by' => auth()->id()],
            ])->all();

            $offering->students()->sync($syncData);
        });

        return redirect()
            ->route('teacher.subjects.index')
            ->with('success', 'Subject created and students enrolled.');
    }

    public function syncTeacherSubjectStudents(Request $request, ClassSubject $classSubject)
    {
        abort_unless(
            (int) $classSubject->teacher_id === (int) auth()->id(),
            403
        );

        $validated = $request->validate([
            'class_id' => [
                'required',
                Rule::exists('classes', 'id')
                    ->where('created_by', auth()->id()),
            ],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['integer', 'distinct', 'exists:users,id'],
        ]);

        $class = SchoolClass::query()
            ->whereKey($validated['class_id'])
            ->where('created_by', auth()->id())
            ->firstOrFail();

        $studentIds = collect($validated['student_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $eligibleStudentIds = $class->students()
            ->where('users.role', 'student')
            ->whereIn('users.id', $studentIds)
            ->pluck('users.id')
            ->map(fn ($id) => (int) $id);

        if ($eligibleStudentIds->count() !== $studentIds->count()) {
            return back()->withErrors([
                'student_ids' => 'Select students enrolled in the selected class only.',
            ]);
        }

        DB::transaction(function () use ($class, $studentIds, $classSubject) {
            $classSubject->update([
                'class_id' => $class->id,
            ]);

            $syncData = $studentIds->mapWithKeys(fn ($id) => [
                $id => ['assigned_by' => auth()->id()],
            ])->all();

            $classSubject->students()->sync($syncData);
        });

        return back()->with('success', 'Subject class and student list updated.');
    }
}
