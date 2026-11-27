<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents the AiInsight application component.
 */
class AiInsight extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'generated_by',
        'type',
        'performance_summary',
        'key_findings',
        'recommended_actions',
        'model',
        'generated_at',
    ];

    protected $casts = [
        'key_findings' => 'array',
        'recommended_actions' => 'array',
        'generated_at' => 'datetime',
    ];

    /**
     * Handles exam.
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Handles generator.
     */
    public function generator()
    {
        return $this->belongsTo(
            User::class,
            'generated_by'
        );
    }
}
