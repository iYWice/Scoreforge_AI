<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\SchoolClass;
use Illuminate\Support\Str;
use App\Models\Question;

class ExamController extends Controller
{
    /*
     Display exams
     */

    public function index()
    {
        $exams = Exam::where('created_by', auth()->id())
            ->withCount('questions')
            ->get();

        $subjects = Subject::all();

        $classes = SchoolClass::all();

        return view('teacher.exams.index', compact(
            'exams',
            'subjects',
            'classes'
        ));
    }

    /*
     Show the form for creating an exam
     */
    public function create()
    {
        $classes = SchoolClass::all();
        $subjects = Subject::all();

        return view('teacher.exams.create', compact(
            'classes',
            'subjects'
        ));
    }


    /*
     Store a newly created exam in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'duration' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:1',
        ]);
        $subject = Subject::find($request->subject_id);

        Exam::create([
            'title' => $request->title,
            'exam_code' => $this->generateExamCode($subject->name),
            'subject_id' => $request->subject_id,
            'class_id' => $request->class_id,
            'created_by' => auth()->id(),
            'duration' => $request->duration,
            'passing_score' => $request->passing_score,
            'total_points' => 0,
            'status' => 'draft',
        ]);

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

        $exam->load('attempts.student');

        return view(
            'teacher.exams.show',
            compact('exam')
        );
    }

    /*
     Show the form for editing the specified exam.
     */
    public function edit(Exam $exam)
    {
        if ($exam->created_by != auth()->id()) {
            abort(403);
        }

        $subjects = Subject::all();
        $classes = SchoolClass::all();

        return view(
            'teacher.exams.edit',
            compact(
                'exam',
                'subjects',
                'classes'
            )
        );
    }

    /*
     Update the specified exam in storage.
     */
    public function update(Request $request, Exam $exam)
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403, 'You are not authorized to update this exam.');
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'duration' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:1',
        ]);

        $exam->update($request->only([

            'title',

            'subject_id',

            'class_id',

            'duration',

            'passing_score',

        ]));

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

        $exam->delete();

        return redirect()
            ->route('teacher.exams.index')
            ->with(
                'success',
                'Exam deleted successfully.'
            );
    }
    private function generateExamCode($subjectName)
    {
        $prefix = strtoupper(substr($subjectName, 0, 4));

        do {
            $random = strtoupper(\Illuminate\Support\Str::random(4));
            $code = $prefix . '-' . $random;
        } while (\App\Models\Exam::where('exam_code', $code)->exists());

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

        $exam->update([
            'status' => $request->status,
        ]);

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
