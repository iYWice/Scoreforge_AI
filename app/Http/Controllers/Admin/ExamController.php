<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
{
    $student = auth()->user();

    $classes = $student->enrolledClasses()
        ->with([
            'teacher',
            'classSubjects' => function ($query) use ($student) {
                $query->whereHas('students', function ($students) use ($student) {
                    $students->where('users.id', $student->id);
                })->with('subject');
            },
        ])
        ->withCount([
            'exams as published_exams_count' => function ($query) {
                $query->where('status', 'published');
            },
        ])
        ->latest('class_enrollments.created_at')
        ->get();

    return view('student.classes.index', compact('classes'));
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
