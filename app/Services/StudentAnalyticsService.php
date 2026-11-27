<?php

namespace App\Services;

use App\Models\ExamAttempt;
use App\Models\StudentAnalytics;

class StudentAnalyticsService
{
    public function updateStudentAnalytics(int $studentId): StudentAnalytics
    {
        $attempts = ExamAttempt::where('student_id', $studentId)
            ->where('status', 'completed')
            ->orderBy('submitted_at')
            ->get();

        if ($attempts->isEmpty()) {
            return StudentAnalytics::updateOrCreate(
                ['student_id' => $studentId],
                [
                    'average_score' => 0,
                    'improvement_rate' => 0,
                    'last_updated' => now(),
                ]
            );
        }

        $percentages = $attempts->map(function ($attempt) {
            if (!$attempt->total_score || $attempt->total_score <= 0) {
                return 0;
            }

            return ($attempt->score / $attempt->total_score) * 100;
        });

        $averageScore = $percentages->average();

        $improvementRate = 0;

        if ($percentages->count() >= 2) {
            $previousScore = $percentages[$percentages->count() - 2];
            $latestScore = $percentages->last();

            $improvementRate = $latestScore - $previousScore;
        }

        $analytics = StudentAnalytics::updateOrCreate(
            ['student_id' => $studentId],
            [
                'average_score' => round($averageScore, 2),
                'improvement_rate' => round($improvementRate, 2),
                'last_updated' => now(),
            ]
        );

        $this->updateRankings();

        return $analytics;
    }
    public function updateRankings(): void
    {
        $analytics = StudentAnalytics::orderByDesc('average_score')->get();

        foreach ($analytics as $index => $studentAnalytics) {
            $studentAnalytics->update([
                'rank_position' => $index + 1,
            ]);
        }
    }
}
