<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents the ExamAnswer application component.
 */
class ExamAnswer extends Model
{
    protected $fillable = [
        'attempt_id',
        'question_id',
        'student_answer',
        'is_correct',
    ];

    /**
     * Handles attempt.
     */
    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    /**
     * Handles question.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Handles answers.
     */
    public function answers()
    {
        return $this->hasMany(ExamAnswer::class, 'attempt_id');
    }
}
