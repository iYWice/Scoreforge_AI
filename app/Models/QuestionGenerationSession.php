<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionGenerationSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exam_id',
        'source_name',
        'source_file',
        'purpose',
        'question_count',
        'question_types',
        'difficulty',
        'generated_questions',
        'status',
    ];

    protected $casts = [
        'question_types' => 'array',
        'generated_questions' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
