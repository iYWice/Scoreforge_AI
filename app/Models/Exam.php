<?php

namespace App\Models;

use App\Models\ExamAttempt;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'title',
        'exam_code',
        'subject_id',
        'class_id',
        'exam_code',
        'time_limit',
        'total_items',
        'created_by',
        'created_at',
        'status',
        'duration',
        'passing_score',
    ];


    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'exam_id');
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class, 'exam_id');
    }

    public function aiInsights()
    {
        return $this->hasMany(AiInsight::class);
    }
}
