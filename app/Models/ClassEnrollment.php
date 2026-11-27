<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents the ClassEnrollment application component.
 */
class ClassEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'student_id',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    /**
     * Handles schoolClass.
     */
    public function schoolClass()
    {
        return $this->belongsTo(
            SchoolClass::class,
            'class_id'
        );
    }

    /**
     * Handles student.
     */
    public function student()
    {
        return $this->belongsTo(
            User::class,
            'student_id'
        );
    }
}
