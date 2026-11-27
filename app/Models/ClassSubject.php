<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents the ClassSubject application component.
 */
class ClassSubject extends Model
{
    protected $fillable = [
        'class_id',
        'subject_id',
        'teacher_id',
        'edp_code',
        'schedule',
    ];

    /**
     * Handles schoolClass.
     */
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Handles subject.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Handles teacher.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Handles students.
     */
    public function students()
    {
        return $this->belongsToMany(
            User::class,
            'subject_enrollments',
            'class_subject_id',
            'student_id'
        )->withPivot('assigned_by')->withTimestamps();
    }
}
