<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\StudentAnalytics;
use App\Services\ExamAnalyticsService;
use App\Models\Prediction;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use App\Models\AiInsight;
use Throwable;

class AnalyticsController extends Controller
{
    public function index()
    {
        $teacherId = auth()->id();

        // Only exams created by this teacher
        $examIds = Exam::where('created_by', $teacherId)
            ->pluck('id');

        // Completed attempts from teacher's exams
        $attempts = ExamAttempt::with([
            'student',
            'exam.subject',
        ])
            ->whereIn('exam_id', $examIds)
            ->where('status', 'completed')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Basic Statistics
        |--------------------------------------------------------------------------
        */

        $totalAttempts = $attempts->count();

        $percentages = $attempts->map(function ($attempt) {
            if (($attempt->total_score ?? 0) <= 0) {
                return 0;
            }

            return (($attempt->score ?? 0)
                / $attempt->total_score) * 100;
        });

        $averageScore = $percentages->isNotEmpty()
            ? round($percentages->average(), 2)
            : 0;

        $highestScore = $percentages->isNotEmpty()
            ? round($percentages->max(), 2)
            : 0;

        $lowestScore = $percentages->isNotEmpty()
            ? round($percentages->min(), 2)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Pass / Fail
        |--------------------------------------------------------------------------
        */

        $passed = $attempts->filter(function ($attempt) {
            return ($attempt->score ?? 0)
                >= ($attempt->exam->passing_score ?? 0);
        })->count();

        $failed = $totalAttempts - $passed;

        $passRate = $totalAttempts > 0
            ? round(($passed / $totalAttempts) * 100, 2)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Student Rankings
        |--------------------------------------------------------------------------
        */

        $studentRankings = $attempts
            ->groupBy('student_id')
            ->map(function ($studentAttempts) {

                $student = $studentAttempts->first()->student;

                $scores = $studentAttempts->map(function ($attempt) {
                    if (($attempt->total_score ?? 0) <= 0) {
                        return 0;
                    }

                    return (($attempt->score ?? 0)
                        / $attempt->total_score) * 100;
                });

                return [
                    'student' => $student,
                    'average' => round($scores->average(), 2),
                    'attempts' => $studentAttempts->count(),
                ];
            })
            ->sortByDesc('average')
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Student Trends
        |--------------------------------------------------------------------------
        */

        $improvingStudents = StudentAnalytics::with('student')
            ->whereIn(
                'student_id',
                $attempts->pluck('student_id')->unique()
            )
            ->where('improvement_rate', '>', 0)
            ->orderByDesc('improvement_rate')
            ->get();

        $decliningStudents = StudentAnalytics::with('student')
            ->whereIn(
                'student_id',
                $attempts->pluck('student_id')->unique()
            )
            ->where('improvement_rate', '<', 0)
            ->orderBy('improvement_rate')
            ->get();

        return view('teacher.analytics.index', compact(
            'totalAttempts',
            'averageScore',
            'highestScore',
            'lowestScore',
            'passed',
            'failed',
            'passRate',
            'studentRankings',
            'improvingStudents',
            'decliningStudents'
        ));
    }

    public function exam(
        Exam $exam,
        ExamAnalyticsService $analyticsService
    ) {
        if ($exam->created_by !== auth()->id()) {
            abort(403);
        }

        $exam->load([
            'subject',
            'class',
        ]);

        $analytics = $analyticsService->analyze($exam);

        return view(
            'teacher.analytics.exam',
            compact('exam', 'analytics')
        );
    }
    public function predictions()
    {
        $teacherId = auth()->id();

        /*
    |--------------------------------------------------------------------------
    | Students who have completed this teacher's exams
    |--------------------------------------------------------------------------
    */

        $studentIds = \App\Models\ExamAttempt::whereHas(
            'exam',
            function ($query) use ($teacherId) {
                $query->where('created_by', $teacherId);
            }
        )
            ->where('status', 'completed')
            ->pluck('student_id')
            ->unique();

        /*
    |--------------------------------------------------------------------------
    | Overall predictions
    |--------------------------------------------------------------------------
    */

        $predictions = Prediction::with('student')
            ->whereIn('student_id', $studentIds)
            ->whereNull('subject_id')
            ->orderByDesc('predicted_score')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | Likely Achievers
    |--------------------------------------------------------------------------
    */

        $achievers = $predictions
            ->filter(function ($prediction) {
                return $prediction->predicted_score >= 90;
            })
            ->values();

        /*
    |--------------------------------------------------------------------------
    | Potential Achievers
    |--------------------------------------------------------------------------
    */

        $potentialAchievers = $predictions
            ->filter(function ($prediction) {
                return $prediction->predicted_score >= 80
                    && $prediction->predicted_score < 90;
            })
            ->values();

        /*
    |--------------------------------------------------------------------------
    | At-Risk Students
    |--------------------------------------------------------------------------
    */

        $atRiskStudents = $predictions
            ->filter(function ($prediction) {
                return in_array(
                    $prediction->risk_level,
                    ['High', 'Moderate']
                );
            })
            ->sortBy('predicted_score')
            ->values();

        return view(
            'teacher.analytics.predictions',
            compact(
                'predictions',
                'achievers',
                'potentialAchievers',
                'atRiskStudents'
            )
        );
    }

    public function generateAiInsight(
        Exam $exam,
        ExamAnalyticsService $analyticsService,
        GeminiService $geminiService
    ) {
        /*
    |--------------------------------------------------------------------------
    | Authorization
    |--------------------------------------------------------------------------
    */

        if ($exam->created_by !== auth()->id()) {
            abort(403);
        }

        $exam->load([
            'subject',
            'class',
        ]);

        /*
    |--------------------------------------------------------------------------
    | Check for existing saved insight
    |--------------------------------------------------------------------------
    */

        $existingInsight = AiInsight::where(
            'exam_id',
            $exam->id
        )
            ->where('type', 'teacher_exam')
            ->latest('generated_at')
            ->first();

        if ($existingInsight) {

            $analytics = $analyticsService->analyze($exam);

            return view(
                'teacher.analytics.ai-insight',
                [
                    'exam' => $exam,
                    'analytics' => $analytics,
                    'aiInsight' => $existingInsight,
                ]
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Calculate authoritative AI-Q analytics
    |--------------------------------------------------------------------------
    */

        $analytics = $analyticsService->analyze($exam);

        if (($analytics['total_attempts'] ?? 0) <= 0) {

            return back()->with(
                'error',
                'AI-Q needs at least one completed examination response before generating an AI insight.'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Generate Gemini analysis
    |--------------------------------------------------------------------------
    */

        try {

            $generated = $geminiService->generateExamInsight(
                $exam,
                $analytics
            );

            /*
        |--------------------------------------------------------------------------
        | Save Gemini result
        |--------------------------------------------------------------------------
        */

            $aiInsight = AiInsight::create([
                'exam_id' => $exam->id,

                'generated_by' => auth()->id(),

                'type' => 'teacher_exam',

                'performance_summary' =>
                $generated['performance_summary'],

                'key_findings' =>
                $generated['key_findings'],

                'recommended_actions' =>
                $generated['recommended_actions'],

                'model' =>
                config('services.gemini.model'),

                'generated_at' => now(),
            ]);

            return view(
                'teacher.analytics.ai-insight',
                compact(
                    'exam',
                    'analytics',
                    'aiInsight'
                )
            );
        } catch (\Throwable $exception) {

            report($exception);

            return back()->with(
                'error',
                'AI-Q could not generate the AI insight right now. Your examination analytics are still available.'
            );
        }
    }

    public function regenerateAiInsight(
        Exam $exam,
        ExamAnalyticsService $analyticsService,
        GeminiService $geminiService
    ) {
        if ($exam->created_by !== auth()->id()) {
            abort(403);
        }

        $exam->load([
            'subject',
            'class',
        ]);

        $analytics = $analyticsService->analyze($exam);

        if (($analytics['total_attempts'] ?? 0) <= 0) {

            return back()->with(
                'error',
                'AI-Q needs completed examination responses before regenerating an AI insight.'
            );
        }

        try {

            $generated = $geminiService->generateExamInsight(
                $exam,
                $analytics
            );

            $aiInsight = AiInsight::create([
                'exam_id' => $exam->id,
                'generated_by' => auth()->id(),
                'type' => 'teacher_exam',

                'performance_summary' =>
                $generated['performance_summary'],

                'key_findings' =>
                $generated['key_findings'],

                'recommended_actions' =>
                $generated['recommended_actions'],

                'model' =>
                config('services.gemini.model'),

                'generated_at' => now(),
            ]);

            return view(
                'teacher.analytics.ai-insight',
                compact(
                    'exam',
                    'analytics',
                    'aiInsight'
                )
            );
        } catch (\RuntimeException $exception) {

            report($exception);

            return back()->with(
                'error',
                $exception->getMessage()
            );
        } catch (\Throwable $exception) {

            report($exception);

            return back()->with(
                'error',
                'AI-Q could not generate the AI insight right now.'
            );
        }
    }
}
