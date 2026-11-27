<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents the Recommendation application component.
 */
class Recommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'recommendation_text',
    ];

    /**
     * Handles student.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Handles subject.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
