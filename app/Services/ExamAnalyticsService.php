<?php

namespace App\Services;

use App\Models\Exam;

class ExamAnalyticsService
{
    public function analyze(Exam $exam): array
    {
        $exam->load([
            'questions',
            'attempts' => function ($query) {
                $query->where('status', 'completed')
                    ->with([
                        'student',
                        'answers.question',
                    ]);
            },
        ]);

        $attempts = $exam->attempts;

        /*
        |--------------------------------------------------------------------------
        | Exam Statistics
        |--------------------------------------------------------------------------
        */

        $percentages = $attempts->map(function ($attempt) {
            if (($attempt->total_score ?? 0) <= 0) {
                return 0;
            }

            return (($attempt->score ?? 0)
                / $attempt->total_score) * 100;
        });

        $totalStudents = $attempts
            ->pluck('student_id')
            ->unique()
            ->count();

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

        $passed = $attempts->filter(function ($attempt) use ($exam) {
            return ($attempt->score ?? 0)
                >= ($exam->passing_score ?? 0);
        })->count();

        $failed = $attempts->count() - $passed;

        $passRate = $attempts->count() > 0
            ? round(
                ($passed / $attempts->count()) * 100,
                2
            )
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Question / Item Analysis
        |--------------------------------------------------------------------------
        */

        $questionAnalysis = [];

        foreach ($exam->questions as $question) {

            $answers = $attempts
                ->flatMap->answers
                ->where('question_id', $question->id);

            $totalAnswers = $answers->count();

            $correct = $answers
                ->where('is_correct', true)
                ->count();

            $incorrect = $answers
                ->where('is_correct', false)
                ->count();

            $accuracy = $totalAnswers > 0
                ? round(($correct / $totalAnswers) * 100, 2)
                : 0;

            $difficulty = $this->getDifficulty(
                $accuracy,
                $totalAnswers
            );

            $questionAnalysis[] = [
                'question_id' => $question->id,
                'question_text' => $question->question_text,
                'topic' => $question->topic,
                'correct' => $correct,
                'incorrect' => $incorrect,
                'total_answers' => $totalAnswers,
                'accuracy' => $accuracy,
                'difficulty' => $difficulty,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Topic Analysis
        |--------------------------------------------------------------------------
        */

        $topicAnalysis = collect($questionAnalysis)
            ->filter(fn($question) => !empty($question['topic']))
            ->groupBy('topic')
            ->map(function ($questions, $topic) {

                $correct = $questions->sum('correct');
                $total = $questions->sum('total_answers');

                $accuracy = $total > 0
                    ? round(($correct / $total) * 100, 2)
                    : 0;

                return [
                    'topic' => $topic,
                    'correct' => $correct,
                    'total' => $total,
                    'accuracy' => $accuracy,
                ];
            })
            ->sortBy('accuracy')
            ->values()
            ->toArray();

        return [
            'total_students' => $totalStudents,
            'total_attempts' => $attempts->count(),

            'average_score' => $averageScore,
            'highest_score' => $highestScore,
            'lowest_score' => $lowestScore,

            'passed' => $passed,
            'failed' => $failed,
            'pass_rate' => $passRate,

            'question_analysis' => $questionAnalysis,
            'topic_analysis' => $topicAnalysis,
        ];
    }

    private function getDifficulty(
        float $accuracy,
        int $totalAnswers
    ): string {
        if ($totalAnswers === 0) {
            return 'No Data';
        }

        if ($accuracy >= 80) {
            return 'Easy';
        }

        if ($accuracy >= 50) {
            return 'Moderate';
        }

        return 'Difficult';
    }
}
