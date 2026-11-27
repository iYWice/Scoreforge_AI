<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents the StudentPerformanceAnalysis application component.
 */
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

    /**
     * Handles student.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Handles teacher.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Handles attempt.
     */
    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'exam_attempt_id');
    }
}
