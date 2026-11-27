<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents the Subject application component.
 */
class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Handles exams.
     */
    public function exams()
    {
        return $this->hasMany(Exam::class, 'subject_id');
    }

    /**
     * Handles classSubjects.
     */
    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class);
    }
}
