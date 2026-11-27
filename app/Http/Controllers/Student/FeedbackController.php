<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentPerformanceAnalysis;

/**
 * Represents the FeedbackController application component.
 */
class FeedbackController extends Controller
{
    /**
     * Handles index.
     */
    public function index()
    {
        $analyses = StudentPerformanceAnalysis::with('attempt.exam.subject')
            ->where('student_id', auth()->id())
            ->whereHas('attempt.exam', fn ($query) => $query->availableToStudent(auth()->user()))
            ->whereNotNull('sent_at')
            ->latest('sent_at')
            ->paginate(15);

        return view('student.feedback.index', compact('analyses'));
    }
}
