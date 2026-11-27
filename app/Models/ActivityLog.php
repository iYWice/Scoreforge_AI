<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Represents the ActivityLog application component.
 */
class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'ip_address',
        'user_agent',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Handles user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Handles subject.
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
