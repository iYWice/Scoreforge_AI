<?php

namespace App\Services;

use App\Models\ExamAttempt;
use App\Models\Prediction;
use App\Services\GeminiService;

class PerformancePredictionService
{
    public function __construct(
        private readonly GeminiService $geminiService
    ) {}
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
        $predictionData = [
            'completed_exams' => $attempts->count(),
            'score_percentages' => $percentages
                ->map(fn($score) => round((float) $score, 2))
                ->values()
                ->all(),
            'overall_average' => round($average, 2),
            'recent_average' => round($recentAverage, 2),
            'trend_adjustment' => round($trendAdjustment, 2),
        ];

        try {
            $prompt = 'You are assisting with student performance forecasting. '
                . 'Use only the supplied examination percentages and summary statistics. '
                . 'Do not infer causes or use information not supplied. '
                . 'Return only JSON with these keys: predicted_score (number 0-100), '
                . 'predicted_level (Excellent, Very Good, Good, Needs Improvement, or At Risk), '
                . 'risk_level (Low, Moderate, or High), '
                . 'confidence_level (Low, Moderate, or High), and reason (brief evidence-based text). '
                . 'Treat this as an estimate based on limited examination history. '
                . 'Data: ' . json_encode($predictionData, JSON_PRETTY_PRINT);

            $rawPrediction = trim($this->geminiService->generate($prompt));
            $rawPrediction = preg_replace('/^```(?:json)?\s*/i', '', $rawPrediction);
            $rawPrediction = preg_replace('/\s*```$/', '', $rawPrediction);

            $aiPrediction = json_decode($rawPrediction, true);

            $validLevels = [
                'Excellent',
                'Very Good',
                'Good',
                'Needs Improvement',
                'At Risk',
            ];

            if (
                is_array($aiPrediction) &&
                is_numeric($aiPrediction['predicted_score'] ?? null) &&
                in_array($aiPrediction['predicted_level'] ?? null, $validLevels, true) &&
                in_array($aiPrediction['risk_level'] ?? null, ['Low', 'Moderate', 'High'], true) &&
                in_array($aiPrediction['confidence_level'] ?? null, ['Low', 'Moderate', 'High'], true) &&
                is_string($aiPrediction['reason'] ?? null)
            ) {
                $predictedScore = max(
                    0,
                    min(100, (float) $aiPrediction['predicted_score'])
                );
                $predictedLevel = $aiPrediction['predicted_level'];
                $riskLevel = $aiPrediction['risk_level'];
                $confidence = $aiPrediction['confidence_level'];
                $reason = mb_substr($aiPrediction['reason'], 0, 1000);
            }
        } catch (\Throwable $exception) {
            report($exception);
            // Keep the existing score-based prediction if Gemini is unavailable.
        }

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
