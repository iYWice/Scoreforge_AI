<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicReadiness;
use App\Models\ExamAttempt;
use App\Models\Prediction;
use App\Models\Recommendation;
use App\Models\StudentAnalytics;
use App\Services\StudentPerformanceService;

/**
 * Represents the DashboardController application component.
 */
class DashboardController extends Controller
{
    /**
     * Handles index.
     */
    public function index(StudentPerformanceService $performanceService)
    {
        $studentId = auth()->id();

        $attempts = ExamAttempt::with('exam')
            ->where('student_id', $studentId)
            ->where('status', 'completed')
            ->whereHas('exam', fn ($query) => $query->availableToStudent(auth()->user()))
            ->latest('submitted_at')
            ->get();

        $analytics = StudentAnalytics::where(
            'student_id',
            $studentId
        )->first();

        $latestRecommendation =
            Recommendation::where(
                'student_id',
                $studentId
            )
                ->with('subject')
                ->latest()
                ->first();

        $performance =
            $performanceService
                ->getStudentPerformance(
                    $studentId
                );

        $prediction = Prediction::where(
            'student_id',
            auth()->id()
        )
            ->whereNull('subject_id')
            ->latest('updated_at')
            ->first();

        $readiness = AcademicReadiness::where(
            'student_id',
            auth()->id()
        )->first();

        return view(
            'student.dashboard',
            compact(
                'attempts',
                'analytics',
                'latestRecommendation',
                'performance',
                'prediction',
                'readiness'
            )
        );
    }

    /**
     * Handles history.
     */
    public function history()
    {
        $attempts = ExamAttempt::with('exam.subject')
            ->where('student_id', auth()->id())
            ->where('status', 'completed')
            ->whereHas('exam', fn ($query) => $query->availableToStudent(auth()->user()))
            ->latest('submitted_at')
            ->paginate(15);

        return view('student.exam-history', compact('attempts'));
    }
}
