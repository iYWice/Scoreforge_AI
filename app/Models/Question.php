<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents the Question application component.
 */
class Question extends Model
{
    use HasFactory;

    protected $fillable = [

        'exam_id',
        'question_type',
        'topic',
        'question_text',
        'correct_answer',
        'points',

    ];

    /**
     * Handles exam.
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    /**
     * Handles options.
     */
    public function options()
    {
        return $this->hasMany(QuestionOption::class);
    }

    /**
     * Handles answers.
     */
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
