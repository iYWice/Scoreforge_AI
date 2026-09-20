<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'average_score',
        'improvement_rate',
        'rank_position',
        'last_updated',
    ];

    protected $casts = [
        'last_updated' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
