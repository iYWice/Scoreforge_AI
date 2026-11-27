<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents the Answer application component.
 */
class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'exam_code',
        'subject_id',
        'class_id',
        'created_by',
        'duration',
        'total_points',
        'passing_score',
        'status',
        'attempt_id',
        'question_id',
        'answer_text',
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
        return $this->belongsTo(
            Question::class,
            'question_id'
        );
    }
}
