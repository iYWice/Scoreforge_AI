<?php

namespace App\Services;

use App\Models\ExamAttempt;
use App\Models\StudentAnalytics;

class StudentPerformanceService
{
    public function getStudentPerformance(int $studentId): array
    {
        $attempts = ExamAttempt::with([
            'exam.subject',
            'answers.question',
        ])
            ->where('student_id', $studentId)
            ->where('status', 'completed')
            ->orderBy('submitted_at')
            ->orderBy('id')
            ->get();

        if ($attempts->isEmpty()) {
            return [
                'latest_percentage' => 0,
                'average_percentage' => 0,
                'correct_answers' => 0,
                'incorrect_answers' => 0,
                'performance_level' => 'No Data',
                'trend' => 'No Data',
                'subjects' => [],
                'weak_topics' => [],
                'strong_topics' => [],
            ];
        }

        $percentages = $attempts->map(function ($attempt) {
            if (($attempt->total_score ?? 0) <= 0) {
                return 0;
            }

            return (($attempt->score ?? 0) / $attempt->total_score) * 100;
        });

        $allAnswers = $attempts->flatMap->answers;
        $topicPerformance = $this->getTopicPerformance($attempts);

        return [
            'latest_percentage' => round($percentages->last(), 2),
            'average_percentage' => round($percentages->average(), 2),
            'correct_answers' => $allAnswers->where('is_correct', true)->count(),
            'incorrect_answers' => $allAnswers->where('is_correct', false)->count(),
            'performance_level' => $this->getPerformanceLevel($percentages->average()),
            'trend' => $this->getTrend($percentages->values()->all()),
            'subjects' => $this->getSubjectPerformance($attempts),

            // Return both lists; the dashboard uses both.
            'weak_topics' => $topicPerformance
                ->sortBy('percentage')
                ->values()
                ->all(),

            'strong_topics' => $topicPerformance
                ->sortByDesc('percentage')
                ->values()
                ->all(),
        ];
    }

    private function getTopicPerformance($attempts)
    {
        $stats = [];

        foreach ($attempts as $attempt) {
            foreach ($attempt->answers as $answer) {
                $rawTopic = trim((string) $answer->question?->topic);

                if ($rawTopic === '') {
                    continue;
                }

                $topic = preg_replace('/\s+/u', ' ', $rawTopic) ?? $rawTopic;
                $key = strtolower($topic);

                if (!isset($stats[$key])) {
                    $stats[$key] = [
                        'topic' => $topic,
                        'correct' => 0,
                        'incorrect' => 0,
                        'total' => 0,
                    ];
                }

                $stats[$key]['total']++;

                if ((bool) $answer->is_correct) {
                    $stats[$key]['correct']++;
                } else {
                    $stats[$key]['incorrect']++;
                }
            }
        }

        return collect($stats)->map(function ($topic) {
            $topic['percentage'] = $topic['total'] > 0
                ? round(($topic['correct'] / $topic['total']) * 100, 1)
                : 0;

            return $topic;
        })->values();
    }

    private function getPerformanceLevel(float $percentage): string
    {
        if ($percentage >= 90) return 'Excellent';
        if ($percentage >= 80) return 'Very Good';
        if ($percentage >= 75) return 'Good';
        if ($percentage >= 60) return 'Needs Improvement';

        return 'At Risk';
    }

    private function getTrend(array $percentages): string
    {
        if (count($percentages) < 2) {
            return 'Not Enough Data';
        }

        $previous = $percentages[count($percentages) - 2];
        $latest = $percentages[count($percentages) - 1];

        if ($latest > $previous) return 'Improving';
        if ($latest < $previous) return 'Declining';

        return 'Stable';
    }

    private function getSubjectPerformance($attempts): array
    {
        return $attempts
            ->groupBy(fn ($attempt) => $attempt->exam?->subject_id)
            ->map(function ($subjectAttempts) {
                $subject = $subjectAttempts->first()->exam?->subject;

                $scores = $subjectAttempts->map(function ($attempt) {
                    if (($attempt->total_score ?? 0) <= 0) {
                        return 0;
                    }

                    return (($attempt->score ?? 0) / $attempt->total_score) * 100;
                });

                return [
                    'subject_id' => $subject?->id,
                    'subject_name' => $subject?->name ?? 'Unknown Subject',
                    'average_percentage' => round($scores->average(), 1),
                    'attempts' => $subjectAttempts->count(),
                ];
            })
            ->values()
            ->all();
    }
}