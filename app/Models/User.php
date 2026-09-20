<?php

namespace App\Models;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Prediction;
use App\Models\Recommendation;
use App\Models\StudentAnalytics;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\AcademicReadiness;

#[Fillable([
    'fname',
    'lname',
    'email',
    'password',
    'role',
])]
#[Hidden([
    'password',
    'remember_token',
])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public function examsCreated()
    {
        return $this->hasMany(Exam::class, 'created_by');
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class, 'student_id');
    }

    public function analytics()
    {
        return $this->hasOne(StudentAnalytics::class, 'student_id');
    }

    public function predictions()
    {
        return $this->hasMany(Prediction::class, 'student_id');
    }

    public function recommendations()
    {
        return $this->hasMany(Recommendation::class, 'student_id');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function academicReadiness()
    {
        return $this->hasOne(
            AcademicReadiness::class,
            'student_id'
        );
    }
}
