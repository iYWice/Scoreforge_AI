<?php

use App\Models\Exam;
use App\Models\ExamAttempt;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function (): void {
    Exam::where('status', 'published')
        ->whereNotNull('closes_at')
        ->where('closes_at', '<=', now())
        ->update(['status' => 'closed']);

    ExamAttempt::where('status', 'ongoing')
        ->with('exam')
        ->eachById(function (ExamAttempt $attempt): void {
            $deadline = $attempt->exam->effectiveDeadline($attempt->started_at);

            if (now()->gte($deadline)) {
                $attempt->update(['status' => 'timed_out']);
            }
        });
})
    ->everyMinute()
    ->name('close-expired-exams')
    ->withoutOverlapping();
