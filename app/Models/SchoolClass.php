<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

    public function teacher()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function exams()
    {
        return $this->hasMany(
            Exam::class,
            'class_id'
        );
    }

    public function enrollments()
    {
        return $this->hasMany(
            ClassEnrollment::class,
            'class_id'
        );
    }

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
}
