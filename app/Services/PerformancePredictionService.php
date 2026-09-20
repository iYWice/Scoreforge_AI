<?php

namespace App\Services;

use App\Models\ExamAttempt;
use App\Models\Prediction;

class PerformancePredictionService
{
    public function predict(int $studentId): Prediction
    {
        $attempts = ExamAttempt::with('exam.subject')
            ->where('student_id', $studentId)
            ->where('status', 'completed')
            ->orderBy('submitted_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Not enough historical data
        |--------------------------------------------------------------------------
        */

        if ($attempts->isEmpty()) {
            return Prediction::create([
                'student_id' => $studentId,
                'subject_id' => null,
                'predicted_score' => 0,
                'predicted_level' => 'No Data',
                'risk_level' => 'Unknown',
                'confidence_level' => 'Low',
                'reason' => 'No completed examination data is available.',
            ]);
        }

        $percentages = $attempts->map(function ($attempt) {
            if (($attempt->total_score ?? 0) <= 0) {
                return 0;
            }

            return (($attempt->score ?? 0)
                / $attempt->total_score) * 100;
        })->values();

        $average = (float) $percentages->average();

        /*
        |--------------------------------------------------------------------------
        | Recent Performance
        |--------------------------------------------------------------------------
        */

        $recentScores = $percentages->take(-3);

        $recentAverage = (float) $recentScores->average();

        /*
        |--------------------------------------------------------------------------
        | Trend
        |--------------------------------------------------------------------------
        */

        $trendAdjustment = 0;

        if ($percentages->count() >= 2) {

            $previous = (float)
            $percentages[$percentages->count() - 2];

            $latest = (float)
            $percentages[$percentages->count() - 1];

            $difference = $latest - $previous;

            // Limit trend influence
            $trendAdjustment = max(
                -10,
                min(10, $difference * 0.30)
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Prediction
        |--------------------------------------------------------------------------
        |
        | 40% overall performance
        | 60% recent performance
        | + limited trend adjustment
        |
        */

        $predictedScore =
            ($average * 0.40)
            + ($recentAverage * 0.60)
            + $trendAdjustment;

        $predictedScore = max(
            0,
            min(100, $predictedScore)
        );

        /*
        |--------------------------------------------------------------------------
        | Classification
        |--------------------------------------------------------------------------
        */

        $predictedLevel =
            $this->performanceLevel($predictedScore);

        $riskLevel =
            $this->riskLevel($predictedScore);

        $confidence =
            $this->confidenceLevel(
                $attempts->count()
            );

        $reason = $this->buildReason(
            $average,
            $recentAverage,
            $trendAdjustment,
            $attempts->count()
        );

        /*
        |--------------------------------------------------------------------------
        | Save latest prediction
        |--------------------------------------------------------------------------
        */

        return Prediction::updateOrCreate(
            [
                'student_id' => $studentId,
                'subject_id' => null,
            ],
            [
                'predicted_score' =>
                round($predictedScore, 2),

                'predicted_level' =>
                $predictedLevel,

                'risk_level' =>
                $riskLevel,

                'confidence_level' =>
                $confidence,

                'reason' =>
                $reason,
            ]
        );
    }


    private function performanceLevel(
        float $score
    ): string {

        if ($score >= 90) {
            return 'Excellent';
        }

        if ($score >= 80) {
            return 'Very Good';
        }

        if ($score >= 75) {
            return 'Good';
        }

        if ($score >= 60) {
            return 'Needs Improvement';
        }

        return 'At Risk';
    }


    private function riskLevel(
        float $score
    ): string {

        if ($score < 60) {
            return 'High';
        }

        if ($score < 75) {
            return 'Moderate';
        }

        return 'Low';
    }


    private function confidenceLevel(
        int $attemptCount
    ): string {

        if ($attemptCount >= 5) {
            return 'High';
        }

        if ($attemptCount >= 3) {
            return 'Moderate';
        }

        return 'Low';
    }


    private function buildReason(
        float $average,
        float $recentAverage,
        float $trendAdjustment,
        int $attemptCount
    ): string {

        $trend = 'stable';

        if ($trendAdjustment > 0) {
            $trend = 'improving';
        }

        if ($trendAdjustment < 0) {
            $trend = 'declining';
        }

        return sprintf(
            'Prediction is based on %d completed exam(s), an overall average of %.1f%%, a recent average of %.1f%%, and a %s performance trend.',
            $attemptCount,
            $average,
            $recentAverage,
            $trend
        );
    }
}
