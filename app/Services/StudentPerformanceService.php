<?php

namespace App\Services;

use App\Models\ExamAttempt;

class StudentPerformanceService
{
    public function getStudentPerformance(int $studentId): array
    {
        $attempts = ExamAttempt::with([
            'exam.subject',
            'answers'
        ])
            ->where('student_id', $studentId)
            ->where('status', 'completed')
            ->orderBy('submitted_at')
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
            ];
        }

        $percentages = $attempts->map(function ($attempt) {
            if (($attempt->total_score ?? 0) <= 0) {
                return 0;
            }

            return (
                ($attempt->score ?? 0)
                / $attempt->total_score
            ) * 100;
        });

        $latestPercentage = $percentages->last();
        $averagePercentage = $percentages->average();
        $topicPerformance = $this->getTopicPerformance($attempts);
        $weakTopics = $this->getWeakTopics($attempts);
        $correctAnswers = $attempts
            ->flatMap->answers
            ->where('is_correct', true)
            ->count();

        $incorrectAnswers = $attempts
            ->flatMap->answers
            ->where('is_correct', false)
            ->count();

        $weakTopics = collect($topicPerformance)
            ->sortBy('percentage')
            ->values()
            ->toArray();

        $strongTopics = collect($topicPerformance)
            ->sortByDesc('percentage')
            ->values()
            ->toArray();
        $performanceLevel =
            $this->getPerformanceLevel(
                $averagePercentage
            );

        $trend = $this->getTrend(
            $percentages->all()
        );
        $subjects = $this->getSubjectPerformance(
            $attempts
        );

        return [
            'latest_percentage' => round($latestPercentage, 2),
            'average_percentage' => round($averagePercentage, 2),
            'correct_answers' => $correctAnswers,
            'incorrect_answers' => $incorrectAnswers,
            'performance_level' => $performanceLevel,
            'trend' => $trend,
            'subjects' => $subjects,
            'weak_topics' => $weakTopics,
        ];
    }

    private function getPerformanceLevel(
        float $percentage
    ): string {
        if ($percentage >= 90) {
            return 'Excellent';
        }

        if ($percentage >= 80) {
            return 'Very Good';
        }

        if ($percentage >= 75) {
            return 'Good';
        }

        if ($percentage >= 60) {
            return 'Needs Improvement';
        }

        return 'At Risk';
    }

    private function getTrend(array $percentages): string
    {
        $count = count($percentages);

        if ($count < 2) {
            return 'Not Enough Data';
        }

        $previous = $percentages[$count - 2];
        $latest = $percentages[$count - 1];

        if ($latest > $previous) {
            return 'Improving';
        }

        if ($latest < $previous) {
            return 'Declining';
        }

        return 'Stable';
    }

    private function getSubjectPerformance(
        $attempts
    ): array {
        return $attempts
            ->groupBy(function ($attempt) {
                return $attempt->exam->subject_id;
            })
            ->map(function ($subjectAttempts) {

                $subject =
                    $subjectAttempts
                    ->first()
                    ->exam
                    ->subject;

                $percentages =
                    $subjectAttempts->map(
                        function ($attempt) {

                            if (
                                ($attempt->total_score ?? 0)
                                <= 0
                            ) {
                                return 0;
                            }

                            return (
                                ($attempt->score ?? 0)
                                / $attempt->total_score
                            ) * 100;
                        }
                    );

                return [
                    'subject_id' =>
                    $subject?->id,

                    'subject_name' =>
                    $subject?->name
                        ?? 'Unknown Subject',

                    'average_percentage' =>
                    round(
                        $percentages->average(),
                        2
                    ),

                    'attempts' =>
                    $subjectAttempts->count(),
                ];
            })
            ->values()
            ->toArray();
    }
    private function getWeakTopics($attempts): array
    {
        $topicStats = [];

        foreach ($attempts as $attempt) {
            foreach ($attempt->answers as $answer) {
                $question = $answer->question;

                if (!$question || !$question->topic) {
                    continue;
                }

                $topic = trim($question->topic);

                if (!isset($topicStats[$topic])) {
                    $topicStats[$topic] = [
                        'topic' => $topic,
                        'correct' => 0,
                        'incorrect' => 0,
                        'total' => 0,
                    ];
                }

                $topicStats[$topic]['total']++;

                if ($answer->is_correct) {
                    $topicStats[$topic]['correct']++;
                } else {
                    $topicStats[$topic]['incorrect']++;
                }
            }
        }

        foreach ($topicStats as &$stats) {
            $stats['percentage'] =
                $stats['total'] > 0
                ? round(
                    ($stats['correct'] / $stats['total']) * 100,
                    2
                )
                : 0;
        }

        unset($stats);

        return collect($topicStats)
            ->sortBy('percentage')
            ->values()
            ->toArray();
    }
    private function getTopicPerformance($attempts): array
    {
        $topicStats = [];

        foreach ($attempts as $attempt) {
            foreach ($attempt->answers as $answer) {
                $question = $answer->question;

                if (!$question || !$question->topic) {
                    continue;
                }

                $topic = trim($question->topic);

                if (!isset($topicStats[$topic])) {
                    $topicStats[$topic] = [
                        'topic' => $topic,
                        'correct' => 0,
                        'incorrect' => 0,
                        'total' => 0,
                    ];
                }

                $topicStats[$topic]['total']++;

                if ($answer->is_correct) {
                    $topicStats[$topic]['correct']++;
                } else {
                    $topicStats[$topic]['incorrect']++;
                }
            }
        }

        foreach ($topicStats as &$stats) {
            $stats['percentage'] = $stats['total'] > 0
                ? round(($stats['correct'] / $stats['total']) * 100, 2)
                : 0;
        }

        unset($stats);

        return collect($topicStats)
            ->values()
            ->toArray();
    }
}
