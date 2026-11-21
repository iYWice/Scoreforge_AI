<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassSubject;
use App\Models\Exam;
use App\Models\Question;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ExamController extends Controller
{
    /*
     Display exams
     */

    public function index()
    {
        Exam::closeExpiredForTeacher(auth()->user());

        $exams = Exam::where('created_by', auth()->id())
            ->withCount('questions')
            ->get();

        $classes = SchoolClass::where('created_by', auth()->id())
            ->with([
                'classSubjects' => function ($query) {
                    $query->where('teacher_id', auth()->id())
                        ->with('subject');
                },
            ])
            ->orderBy('name')
            ->get();

        return view('teacher.exams.index', compact('exams', 'classes'));
    }

    /*
     Show the form for creating an exam
     */
    public function create()
    {
        $classes = SchoolClass::where('created_by', auth()->id())
            ->with([
                'classSubjects' => function ($query) {
                    $query->where('teacher_id', auth()->id())
                        ->with('subject');
                },
            ])
            ->orderBy('name')
            ->get();

        return view('teacher.exams.create', compact('classes'));
    }

    /*
     Store a newly created exam in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'class_id' => [
                'required',
                Rule::exists('classes', 'id')
                    ->where('created_by', auth()->id()),
            ],
            'total_items' => ['required', 'integer', 'min:1'],
            'duration' => ['required', 'integer', 'min:1'],
            'opens_at' => ['nullable', 'date'],
            'closes_at' => ['nullable', 'date', 'after:opens_at'],
        ]);

        $offeringExists = ClassSubject::where('class_id', $validated['class_id'])
            ->where('teacher_id', auth()->id())
            ->where('subject_id', $validated['subject_id'])
            ->exists();

        if (! $offeringExists) {
            return back()
                ->withInput()
                ->withErrors(['subject_id' => 'The selected subject is not offered in this class.']);
        }

        $validated['passing_score'] = (int) ceil($validated['total_items'] * 0.75);

        $exam = Exam::create([
            'title' => $validated['title'],
            'exam_code' => $this->generateExamCode(),
            'subject_id' => $validated['subject_id'],
            'class_id' => $validated['class_id'],
            'created_by' => auth()->id(),
            'total_items' => $validated['total_items'],
            'duration' => $validated['duration'],
            'passing_score' => $validated['passing_score'],
            'status' => 'draft',
            'opens_at' => $validated['opens_at'] ?? null,
            'closes_at' => $validated['closes_at'] ?? null,
        ]);

        app(ActivityLogger::class)->log(
            'exam.created',
            'Teacher created an examination.',
            $exam
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Exam created successfully.',
            ]);
        }

        return redirect()
            ->route('teacher.exams.index')
            ->with('success', 'Exam created successfully.');
    }

    /*
     Display the specified exam.
     */
    public function show(Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403, 'You are not authorized to view this exam.');
        }

        $exam->closeIfExpired();
        $exam->load(['attempts.student', 'class.classSubjects.students']);

        $eligibleStudents = collect();

        foreach ($exam->class?->classSubjects ?? collect() as $offering) {
            if ((int) $offering->subject_id !== (int) $exam->subject_id) {
                continue;
            }

            $eligibleStudents = $eligibleStudents
                ->concat($offering->students)
                ->unique('id');
        }

        $attemptedStudentIds = $exam->attempts
            ->pluck('student_id')
            ->filter()
            ->unique();

        $notTakenStudents = $eligibleStudents
            ->whereNotIn('id', $attemptedStudentIds);

        return view(
            'teacher.exams.show',
            compact('exam', 'notTakenStudents')
        );
    }

    /*
     Show the form for editing the specified exam.
     */
    public function edit(Exam $exam)
    {
        if ((int) $exam->created_by !== (int) auth()->id()) {
            abort(403, 'You are not authorized to edit this exam.');
        }

        $classes = SchoolClass::where('created_by', auth()->id())
            ->with([
                'classSubjects' => function ($query) {
                    $query->where('teacher_id', auth()->id())
                        ->with('subject');
                },
            ])
            ->orderBy('name')
            ->get();

        $subjects = Subject::all();

        return view('teacher.exams.edit', compact('exam', 'subjects', 'classes'));
    }

    /*
     Update the specified exam in storage.
     */
    public function update(Request $request, Exam $exam)
    {
        if ((int) $exam->created_by !== (int) auth()->id()) {
            abort(403, 'You are not authorized to update this exam.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'class_id' => [
                'required',
                Rule::exists('classes', 'id')
                    ->where('created_by', auth()->id()),
            ],
            'total_items' => ['required', 'integer', 'min:1'],
            'duration' => ['required', 'integer', 'min:1'],
            'opens_at' => ['nullable', 'date'],
            'closes_at' => ['nullable', 'date', 'after:opens_at'],
        ]);

        $validated['passing_score'] = (int) ceil($validated['total_items'] * 0.75);

        $exam->update($validated);

        app(ActivityLogger::class)->log(
            'exam.updated',
            'Teacher updated an examination.',
            $exam
        );

        return redirect()
            ->route('teacher.exams.index')
            ->with('success', 'Exam updated successfully.');
    }

    /*
     Remove the specified exam from storage.
     */
    public function destroy(Exam $exam)
    {
        if ($exam->created_by != auth()->id()) {
            abort(403);
        }
        app(ActivityLogger::class)->log(
            'exam.deleted',
            'Teacher deleted an examination.',
            $exam
        );
        $exam->delete();

        return redirect()
            ->route('teacher.exams.index')
            ->with(
                'success',
                'Exam deleted successfully.'
            );
    }

    private function generateExamCode(?string $subjectName = null)
    {
        $prefix = $subjectName
            ? strtoupper(substr($subjectName, 0, 4))
            : 'EXAM';

        do {
            $random = strtoupper(Str::random(4));
            $code = $prefix.'-'.$random;
        } while (Exam::where('exam_code', $code)->exists());

        return $code;
    }

    /*
     Update the status of the specified exam.
     */
    public function updateStatus(Request $request, Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403, 'You are not authorized to modify this exam.');
        }

        $request->validate([
            'status' => 'required|in:draft,published,closed',
        ]);

        if (
            $request->status === 'published' &&
            $exam->questions()->count() === 0
        ) {
            return back()->with(
                'error',
                'You cannot publish an exam without questions.'
            );
        }

        if (
            $request->status === 'published' &&
            $exam->closes_at !== null &&
            now()->gte($exam->closes_at)
        ) {
            return back()->with(
                'error',
                'You cannot publish an exam after its deadline.'
            );
        }

        $exam->update([
            'status' => $request->status,
        ]);

        app(ActivityLogger::class)->log(
            $request->status === 'published' ? 'exam.published' : 'exam.status_changed',
            'Teacher changed the examination status.',
            $exam,
            ['status' => $request->status]
        );

        return back()->with(
            'success',
            'Exam status updated successfully.'
        );
    }
    // public function questions(Exam $exam)
    // {
    //     if ($exam->created_by !== auth()->id()) {
    //         abort(403);
    //     }

    //     $questions = $exam->questions;

    //     return view(
    //         'teacher.exams.questions',
    //         compact('exam', 'questions')
    //     );
    // }

    // public function storeQuestion(
    //     Request $request,
    //     Exam $exam
    // ) {
    //     if ($exam->created_by !== auth()->id()) {
    //         abort(403);
    //     }

    //     $type = $request->type;

    //     $choices = null;

    //     if ($type === 'multiple_choice') {

    //         $choices = json_encode([
    //             $request->choice_a,
    //             $request->choice_b,
    //             $request->choice_c,
    //             $request->choice_d,
    //         ]);
    //     }

    //     Question::create([
    //         'exam_id' => $exam->id,
    //         'type' => $type,
    //         'question_text' => $request->question_text,
    //         'choices' => $choices,
    //         'correct_answer' => $request->correct_answer,
    //         'points' => 1,
    //     ]);

    //     // Update exam total items
    //     $exam->update([
    //         'total_items' => $exam->questions()->count()
    //     ]);

    //     return back()->with(
    //         'success',
    //         'Question added successfully.'
    //     );
    // }
}
