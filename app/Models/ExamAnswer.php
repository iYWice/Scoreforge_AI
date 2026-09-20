<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    protected $fillable = [
        'attempt_id',
        'question_id',
        'student_answer',
        'is_correct',
    ];

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
    public function answers()
    {
        return $this->hasMany(ExamAnswer::class, 'attempt_id');
    }
}
