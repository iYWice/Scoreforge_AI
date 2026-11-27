<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $classes = SchoolClass::query()
            ->with(['teacher', 'subject', 'classSubjects.subject'])
            ->withCount(['students', 'exams'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('class_code', 'like', "%{$search}%")
                        ->orWhereHas('teacher', function ($teacher) use ($search) {
                            $teacher->where('fname', 'like', "%{$search}%")
                                ->orWhere('lname', 'like', "%{$search}%");
                        })
                        ->orWhereHas('subject', function ($subject) use ($search) {
                            $subject->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $subjects = Subject::orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->orderBy('fname')->get();

        return view('admin.classes.index', compact('classes', 'subjects', 'teachers'));
    }

    public function show(SchoolClass $class)
    {
        $class->load(['teacher', 'subject']);

        $students = $class->students()
            ->orderBy('lname')
            ->paginate(15);

        $exams = $class->exams()
            ->withCount('attempts')
            ->latest()
            ->get();

        return view('admin.classes.show', compact(
            'class',
            'students',
            'exams'
        ));
    }

    public function store(Request $request, ActivityLogger $logger): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'created_by' => ['required', 'exists:users,id'],
        ]);

        abort_unless(User::whereKey($data['created_by'])->where('role', 'teacher')->exists(), 422);

        $data['class_code'] = SchoolClass::generateClassCode();
        $class = SchoolClass::create($data);

        $logger->log('class.created', 'Administrator created a class.', $class);

        return back()->with('success', 'Class created successfully.');
    }

    public function update(Request $request, SchoolClass $class, ActivityLogger $logger): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'created_by' => ['required', 'exists:users,id'],
        ]);

        abort_unless(User::whereKey($data['created_by'])->where('role', 'teacher')->exists(), 422);

        $class->update($data);
        $logger->log('class.updated', 'Administrator updated a class.', $class);

        return back()->with('success', 'Class updated successfully.');
    }

    public function destroy(SchoolClass $class, ActivityLogger $logger): RedirectResponse
    {
        $className = $class->name;

        if (
            $class->exams()->exists()
            || $class->students()->exists()
            || $class->subject()->exists()
            || $class->classSubjects()->exists()
        ) {
            return back()->withErrors([
                'class' => 'Class cannot be deleted while it has students, subjects, or examinations.',
            ]);
        }

        $class->delete();
        $logger->log('class.deleted', 'Administrator deleted class: '.$className);

        return back()->with('success', 'Class deleted successfully.');
    }
}
