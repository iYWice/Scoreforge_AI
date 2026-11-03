<?php

namespace App\Services;

use App\Models\ExamAttempt;
use App\Models\Recommendation;
use App\Services\StudentPerformanceService;
use App\Services\GeminiService;

class RecommendationService
{
    public function __construct(
        private readonly GeminiService $geminiService
    ) {}
    public function generate(ExamAttempt $attempt): Recommendation
    {
        $studentId = $attempt->student_id;

        $performance = app(
            StudentPerformanceService::class
        )->getStudentPerformance($studentId);

        $percentage = 0;

        if (($attempt->total_score ?? 0) > 0) {
            $percentage =
                (($attempt->score ?? 0)
                    / $attempt->total_score) * 100;
        }

        $weakestSubject = collect(
            $performance['subjects'] ?? []
        )
            ->sortBy('average_percentage')
            ->first();

        $weakestTopic = collect(
            $performance['weak_topics'] ?? []
        )->first();

        $recommendations = [];

        $recommendationData = [
            'latest_exam_percentage' => round($percentage, 2),
            'performance_trend' => $performance['trend'] ?? 'Unknown',
            'performance_level' => $performance['performance_level'] ?? 'Unknown',
            'subjects' => array_slice(
                array_values($performance['subjects'] ?? []),
                0,
                10
            ),
            'weak_topics' => array_slice(
                array_values($performance['weak_topics'] ?? []),
                0,
                10
            ),
        ];

        try {
            $prompt = 'You are an educational study-support assistant. '
                . 'Give up to five brief, practical study recommendations using only the supplied exam metrics, subjects, and weak topics. '
                . 'Do not diagnose students, infer personal circumstances, or invent causes. '
                . 'Return only JSON in this format: {"recommendations":["..."]}. '
                . 'Data: ' . json_encode($recommendationData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            $rawRecommendations = trim($this->geminiService->generate($prompt));
            $rawRecommendations = preg_replace('/^```(?:json)?\s*/i', '', $rawRecommendations);
            $rawRecommendations = preg_replace('/\s*```$/', '', $rawRecommendations);

            $aiResult = json_decode($rawRecommendations, true);
            $aiRecommendations = $aiResult['recommendations'] ?? null;

            if (is_array($aiRecommendations)) {
                $aiRecommendations = array_values(array_filter(
                    $aiRecommendations,
                    fn($item) => is_string($item) && trim($item) !== ''
                ));

                if ($aiRecommendations !== []) {
                    $message = implode(' ', array_slice($aiRecommendations, 0, 5));
                }
            }
        } catch (\Throwable $exception) {
            report($exception);
            // Keep the existing rule-based recommendations if Gemini is unavailable.
        }

        /*
    |--------------------------------------------------------------------------
    | Latest Exam Performance
    |--------------------------------------------------------------------------
    */

        if ($percentage < 50) {
            $recommendations[] =
                'Your latest exam performance needs significant improvement. Review the lesson carefully and practice more questions.';
        } elseif ($percentage < 75) {
            $recommendations[] =
                'Your latest exam result is satisfactory, but you should review the questions you answered incorrectly.';
        } elseif ($percentage < 90) {
            $recommendations[] =
                'You performed well in your latest exam. Continue practicing difficult topics to improve further.';
        } else {
            $recommendations[] =
                'Excellent performance in your latest exam. Maintain your current study habits.';
        }

        /*
    |--------------------------------------------------------------------------
    | Performance Trend
    |--------------------------------------------------------------------------
    */

        if (($performance['trend'] ?? null) === 'Declining') {
            $recommendations[] =
                'Your recent performance is declining. Review your recent lessons and increase your practice time.';
        }

        if (($performance['trend'] ?? null) === 'Improving') {
            $recommendations[] =
                'Your recent performance is improving. Continue using your current study approach.';
        }

        if (($performance['trend'] ?? null) === 'Stable') {
            $recommendations[] =
                'Your performance is currently stable. Try additional practice activities to improve further.';
        }

        /*
    |--------------------------------------------------------------------------
    | Weakest Topic
    |--------------------------------------------------------------------------
    */

        if (
            $weakestTopic &&
            ($weakestTopic['percentage'] ?? 100) < 75
        ) {
            $recommendations[] =
                'Focus on the topic "'
                . $weakestTopic['topic']
                . '" because it is currently your weakest area. '
                . 'You answered '
                . $weakestTopic['incorrect']
                . ' question(s) incorrectly in this topic, with a performance rate of '
                . number_format(
                    $weakestTopic['percentage'],
                    1
                )
                . '%.';
        }

        /*
    |--------------------------------------------------------------------------
    | Weakest Subject
    |--------------------------------------------------------------------------
    */

        if (
            $weakestSubject &&
            ($weakestSubject['average_percentage'] ?? 100) < 75
        ) {
            $recommendations[] =
                'You should also spend more study time on '
                . $weakestSubject['subject_name']
                . ', where your current average is '
                . number_format(
                    $weakestSubject['average_percentage'],
                    1
                )
                . '%.';
        }

        /*
    |--------------------------------------------------------------------------
    | Overall Performance Level
    |--------------------------------------------------------------------------
    */

        if (
            ($performance['performance_level'] ?? null)
            === 'At Risk'
        ) {
            $recommendations[] =
                'Your overall performance currently indicates academic risk. Consider reviewing weak topics regularly and asking your teacher for additional guidance.';
        }

        if (
            ($performance['performance_level'] ?? null)
            === 'Excellent'
        ) {
            $recommendations[] =
                'Your overall academic performance is excellent. Continue maintaining consistent study habits and challenge yourself with advanced exercises.';
        }

        $message = implode(
            ' ',
            $recommendations
        );

        return Recommendation::create([
            'student_id' => $studentId,

            'subject_id' =>
            $attempt->exam->subject_id,

            'recommendation_text' =>
            $message,
        ]);
    }
}
