<?php

namespace App\Services;

use App\Models\ExamAttempt;
use App\Models\Recommendation;
use App\Services\StudentPerformanceService;

class RecommendationService
{
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
