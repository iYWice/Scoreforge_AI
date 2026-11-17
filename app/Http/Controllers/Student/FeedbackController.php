<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentPerformanceAnalysis;

class FeedbackController extends Controller
{
    public function index()
    {
        $analyses = StudentPerformanceAnalysis::with('attempt.exam.subject')
            ->where('student_id', auth()->id())
            ->whereNotNull('sent_at')
            ->latest('sent_at')
            ->paginate(15);

        return view('student.feedback.index', compact('analyses'));
    }
}