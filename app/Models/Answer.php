<?php

namespace App\Models;

use App\Models\ExamAttempt;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(
            \App\Models\Question::class,
            'question_id'
        );
    }
}
