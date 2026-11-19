<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    public function scopeAvailableToStudent(Builder $query, User $student): Builder
    {
        $offering = new ClassSubject;
        $examTable = $query->getModel()->getTable();
        $offeringTable = $offering->getTable();
        $enrollmentTable = $offering->students()->getTable();

        return $query->whereExists(function ($subquery) use (
            $student,
            $examTable,
            $offeringTable,
            $enrollmentTable
        ) {
            $subquery->selectRaw('1')
                ->from($offeringTable.' as eligible_offerings')
                ->join(
                    $enrollmentTable.' as eligible_enrollments',
                    'eligible_enrollments.class_subject_id',
                    '=',
                    'eligible_offerings.id'
                )
                ->whereColumn('eligible_offerings.class_id', $examTable.'.class_id')
                ->whereColumn('eligible_offerings.subject_id', $examTable.'.subject_id')
                ->where('eligible_enrollments.student_id', $student->getKey());
        });
    }

    public function isAvailableToStudent(User $student): bool
    {
        return static::query()
            ->whereKey($this->getKey())
            ->availableToStudent($student)
            ->exists();
    }
}
