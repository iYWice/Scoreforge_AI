<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents the QuestionOption application component.
 */
class QuestionOption extends Model
{
    protected $fillable = [

        'question_id',

        'option_text',

        'is_correct',

    ];

    /**
     * Handles question.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
