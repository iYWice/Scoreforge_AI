<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $exams = Exam::query()
            ->with(['teacher', 'subject', 'class'])
            ->withCount(['questions', 'attempts'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');

                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('exam_code', 'like', "%{$search}%")
                        ->orWhereHas('teacher', function ($teacher) use ($search) {
                            $teacher->where('fname', 'like', "%{$search}%")
                                ->orWhere('lname', 'like', "%{$search}%");
                        });
                });
            })
            ->when(
                in_array($request->input('status'), ['draft', 'published', 'closed'], true),
                fn($query) => $query->where('status', $request->input('status'))
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.exams.index', compact('exams'));
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
}
