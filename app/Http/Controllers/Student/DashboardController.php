<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\StudentAnalytics;
use App\Models\Recommendation;
use App\Services\StudentPerformanceService;

class DashboardController extends Controller
{
    public function index(StudentPerformanceService $performanceService)
    {
        $studentId = auth()->id();

        $attempts = ExamAttempt::with('exam')
            ->where('student_id', $studentId)
            ->where('status', 'completed')
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

        $prediction = \App\Models\Prediction::where(
            'student_id',
            auth()->id()
        )
            ->whereNull('subject_id')
            ->latest('updated_at')
            ->first();

        $readiness = \App\Models\AcademicReadiness::where(
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
}
