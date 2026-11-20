<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $exams = Exam::query()
            ->with(['teacher', 'subject', 'class'])
            ->withCount(['questions', 'attempts'])
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->input('search');
                $query->where(function ($q) use ($search): void {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('exam_code', 'like', "%{$search}%")
                        ->orWhereHas('teacher', function ($teacher) use ($search): void {
                            $teacher->where('fname', 'like', "%{$search}%")
                                ->orWhere('lname', 'like', "%{$search}%");
                        });
                });
            })
            ->when(in_array($request->input('status'), ['draft', 'published', 'closed'], true), function ($query) use ($request): void {
                $query->where('status', $request->input('status'));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $subjects = Subject::orderBy('name')->get();
        $classes = SchoolClass::with('teacher')->orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->orderBy('fname')->get();

        return view('admin.exams.index', compact('exams', 'subjects', 'classes', 'teachers'));
    }

    public function show(Exam $exam)
    {
        $exam->load(['teacher', 'subject', 'class']);

        $exam->loadCount(['questions', 'attempts']);

        $questions = $exam->questions()
            ->with('options')
            ->get();

        return view('admin.exams.show', compact(
            'exam',
            'questions'
        ));
    }

    public function store(Request $request, ActivityLogger $logger): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'class_id' => ['required', 'exists:classes,id'],
            'created_by' => ['required', 'exists:users,id'],
            'total_items' => ['required', 'integer', 'min:1'],
            'duration' => ['required', 'integer', 'min:1'],
            'passing_score' => ['required', 'integer', 'min:0', 'max:100'],
            'status' => ['required', 'in:draft,published,closed'],
        ]);

        abort_unless(User::whereKey($data['created_by'])->where('role', 'teacher')->exists(), 422);

        do {
            $examCode = strtoupper(Str::random(8));
        } while (Exam::where('exam_code', $examCode)->exists());

        $exam = Exam::create(array_merge($data, ['exam_code' => $examCode]));
        $logger->log('exam.created', 'Administrator created an examination.', $exam);

        return back()->with('success', 'Examination created successfully.');
    }

    public function update(Request $request, Exam $exam, ActivityLogger $logger): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'class_id' => ['required', 'exists:classes,id'],
            'created_by' => ['required', 'exists:users,id'],
            'total_items' => ['required', 'integer', 'min:1'],
            'duration' => ['required', 'integer', 'min:1'],
            'passing_score' => ['required', 'integer', 'min:0', 'max:100'],
            'status' => ['required', 'in:draft,published,closed'],
        ]);

        abort_unless(User::whereKey($data['created_by'])->where('role', 'teacher')->exists(), 422);

        $exam->update($data);
        $logger->log('exam.updated', 'Administrator updated an examination.', $exam);

        return back()->with('success', 'Examination updated successfully.');
    }

    public function destroy(Exam $exam, ActivityLogger $logger): RedirectResponse
    {
        $examTitle = $exam->title;

        if ($exam->attempts()->exists()) {
            return back()->withErrors([
                'exam' => 'Examination cannot be deleted while it has student attempts.',
            ]);
        }

        $logger->log('exam.deleted', 'Administrator deleted examination: '.$examTitle);
        $exam->delete();

        return back()->with('success', 'Examination deleted successfully.');
    }
}
