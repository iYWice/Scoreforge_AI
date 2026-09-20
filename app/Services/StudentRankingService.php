<?php

namespace App\Services;

use App\Models\StudentAnalytics;

class StudentRankingService
{
    public function updateRankings(): void
    {
        $analytics = StudentAnalytics::query()
            ->whereHas('student', function ($query) {
                $query->where('role', 'student');
            })
            ->orderByDesc('average_score')
            ->get();

        $position = 0;
        $rank = 0;
        $previousAverage = null;

        foreach ($analytics as $studentAnalytics) {
            $position++;

            $average = (float) $studentAnalytics->average_score;

            if (
                $previousAverage === null ||
                $average !== $previousAverage
            ) {
                $rank = $position;
            }

            $studentAnalytics->update([
                'rank_position' => $rank,
            ]);

            $previousAverage = $average;
        }
    }
}
