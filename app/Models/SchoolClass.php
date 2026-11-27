<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Represents the SchoolClass application component.
 */
class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'class_code',
        'created_by',
        'subject_id',
    ];

    /**
     * Handles teacher.
     */
    public function teacher()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    /**
     * Handles subject.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Handles exams.
     */
    public function exams()
    {
        return $this->hasMany(
            Exam::class,
            'class_id'
        );
    }

    /**
     * Handles enrollments.
     */
    public function enrollments()
    {
        return $this->hasMany(
            ClassEnrollment::class,
            'class_id'
        );
    }

    /**
     * Handles students.
     */
    public function students()
    {
        return $this->belongsToMany(
            User::class,
            'class_enrollments',
            'class_id',
            'student_id'
        )->withPivot('joined_at')
            ->withTimestamps();
    }

    /**
     * Handles generateClassCode.
     */
    public static function generateClassCode(): string
    {
        do {
            $code = strtoupper(
                Str::random(6)
            );
        } while (
            self::where('class_code', $code)->exists()
        );

        return $code;
    }

    /**
     * Handles classSubjects.
     */
    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class, 'class_id');
    }
}
