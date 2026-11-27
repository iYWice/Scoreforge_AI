<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'fname',
    'lname',
    'email',
    'password',
    'role',
    'student_id',
    'teacher_id',
])]
#[Hidden([
    'password',
    'remember_token',
])]
/**
 * Represents the User application component.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Handles examsCreated.
     */
    public function examsCreated()
    {
        return $this->hasMany(Exam::class, 'created_by');
    }

    /**
     * Handles attempts.
     */
    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class, 'student_id');
    }

    /**
     * Handles analytics.
     */
    public function analytics()
    {
        return $this->hasOne(StudentAnalytics::class, 'student_id');
    }

    /**
     * Handles predictions.
     */
    public function predictions()
    {
        return $this->hasMany(Prediction::class, 'student_id');
    }

    /**
     * Handles recommendations.
     */
    public function recommendations()
    {
        return $this->hasMany(Recommendation::class, 'student_id');
    }

    /**
     * Handles casts.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Handles academicReadiness.
     */
    public function academicReadiness()
    {
        return $this->hasOne(
            AcademicReadiness::class,
            'student_id'
        );
    }

    /**
     * Handles createdClasses.
     */
    public function createdClasses()
    {
        return $this->hasMany(
            SchoolClass::class,
            'created_by'
        );
    }

    /**
     * Handles enrolledClasses.
     */
    public function enrolledClasses()
    {
        return $this->belongsToMany(
            SchoolClass::class,
            'class_enrollments',
            'student_id',
            'class_id'
        )->withPivot('joined_at')
            ->withTimestamps();
    }
}
