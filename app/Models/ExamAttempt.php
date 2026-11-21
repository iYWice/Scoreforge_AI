<?php

namespace App\Models;

use App\Services\ActivityLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'exam_id',
        'score',
        'status',
        'started_at',
        'submitted_at',
        'total_score',
        'retake_allowed',
        'question_order',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'retake_allowed' => 'boolean',
        'question_order' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'attempt_id');
    }

    public function startExam(Request $request)
    {
        $request->validate([
            'exam_code' => 'required',
        ]);

        $exam = Exam::where(
            'exam_code',
            strtoupper($request->exam_code)
        )
            ->where('status', 'published')
            ->first();

        if (! $exam) {

            return back()->with(
                'error',
                'Invalid or unavailable exam code.'
            );
        }

        return view(
            'student.exam.take',
            compact(
                'attempt',
                'remainingSeconds'
            )
        );
    }

    public function markTimedOutIfExpired(): bool
    {
        if (
            $this->status === 'ongoing' &&
            $this->started_at !== null &&
            now()->gte($this->exam->effectiveDeadline($this->started_at))
        ) {
            $this->update(['status' => 'timed_out']);

            app(ActivityLogger::class)->log(
                'exam.timed_out',
                'Student examination attempt timed out.',
                $this,
                [
                    'exam_id' => $this->exam_id,
                    'deadline' => $this->exam->effectiveDeadline($this->started_at)->toIso8601String(),
                ]
            );

            return true;
        }

        return false;
    }

    public function orderedQuestions()
    {
        $questions = $this->exam->questions;
        $order = $this->question_order ?? [];

        if ($order === []) {
            return $questions;
        }

        $byId = $questions->keyBy('id');

        $ordered = collect($order)
            ->map(fn ($id) => $byId->get((int) $id))
            ->filter();

        return $ordered->concat($questions->whereNotIn('id', $ordered->pluck('id')));
    }
}
