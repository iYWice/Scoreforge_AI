<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicReadiness extends Model
{
    use HasFactory;

    protected $table = 'academic_readiness';

    protected $fillable = [
        'student_id',
        'readiness_score',
        'readiness_level',
        'reason',
        'evaluated_at',
    ];

    protected $casts = [
        'readiness_score' => 'decimal:2',
        'evaluated_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
