<?php

namespace App\Services;

use App\Models\AcademicReadiness;
use App\Models\ExamAttempt;

class AcademicReadinessService
{
    public function evaluate(int $studentId): AcademicReadiness
    {
        $attempts = ExamAttempt::where(
            'student_id',
            $studentId
        )
            ->where('status', 'completed')
            ->orderBy('submitted_at')
            ->get();

        if ($attempts->isEmpty()) {
            return AcademicReadiness::updateOrCreate(
                ['student_id' => $studentId],
                [
                    'readiness_score' => 0,
                    'readiness_level' => 'Insufficient Data',
                    'reason' => 'The student has no completed examinations.',
                    'evaluated_at' => now(),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Calculate percentages
        |--------------------------------------------------------------------------
        */

        $percentages = $attempts->map(function ($attempt) {

            if (($attempt->total_score ?? 0) <= 0) {
                return 0;
            }

            return (
                ($attempt->score ?? 0)
                / $attempt->total_score
            ) * 100;
        })->values();

        $average = (float) $percentages->average();

        /*
        |--------------------------------------------------------------------------
        | Recent performance
        |--------------------------------------------------------------------------
        */

        $recentAverage = (float)
        $percentages
            ->take(-3)
            ->average();

        /*
        |--------------------------------------------------------------------------
        | Performance trend
        |--------------------------------------------------------------------------
        */

        $trend = 'Stable';

        if ($percentages->count() >= 2) {

            $previous =
                $percentages[$percentages->count() - 2];

            $latest =
                $percentages[$percentages->count() - 1];

            if ($latest > $previous) {
                $trend = 'Improving';
            }

            if ($latest < $previous) {
                $trend = 'Declining';
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Readiness Score
        |--------------------------------------------------------------------------
        |
        | 60% overall performance
        | 40% recent performance
        |
        */

        $readinessScore =
            ($average * 0.60)
            + ($recentAverage * 0.40);

        // Small trend adjustment
        if ($trend === 'Improving') {
            $readinessScore += 3;
        }

        if ($trend === 'Declining') {
            $readinessScore -= 3;
        }

        $readinessScore = max(
            0,
            min(100, $readinessScore)
        );

        /*
        |--------------------------------------------------------------------------
        | Classification
        |--------------------------------------------------------------------------
        */

        $level = $this->classify(
            $readinessScore
        );

        $reason = sprintf(
            'The student has an overall average of %.1f%%, a recent average of %.1f%%, and a %s performance trend based on %d completed examination(s).',
            $average,
            $recentAverage,
            strtolower($trend),
            $attempts->count()
        );

        return AcademicReadiness::updateOrCreate(
            ['student_id' => $studentId],
            [
                'readiness_score' =>
                round($readinessScore, 2),

                'readiness_level' =>
                $level,

                'reason' =>
                $reason,

                'evaluated_at' =>
                now(),
            ]
        );
    }


    private function classify(
        float $score
    ): string {

        if ($score >= 85) {
            return 'Ready';
        }

        if ($score >= 75) {
            return 'Conditionally Ready';
        }

        if ($score >= 60) {
            return 'Needs Improvement';
        }

        return 'At Risk';
    }
}
