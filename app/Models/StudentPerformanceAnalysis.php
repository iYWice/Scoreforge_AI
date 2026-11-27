<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentPerformanceAnalysis extends Model
{
    protected $fillable = [
        'student_id',
        'teacher_id',
        'exam_attempt_id',
        'scope',
        'method',
        'content',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'exam_attempt_id');
    }
}