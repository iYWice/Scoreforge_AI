<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents the Prediction application component.
 */
class Prediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'prediction_type',
        'predicted_score',
        'predicted_level',
        'risk_level',
        'confidence_level',
        'reason',
    ];

    protected $casts = [
        'predicted_score' => 'decimal:2',
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
