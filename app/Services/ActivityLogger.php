<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class ActivityLogger
{
    public function log(
        string $action,
        string $description,
        ?Model $subject = null,
        array $properties = []
    ): void {
        try {
            $request = request();

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'description' => $description,
                'subject_type' => $subject?->getMorphClass(),
                'subject_id' => $subject?->getKey(),
                'ip_address' => $request->ip(),
                'user_agent' => substr(
                    (string) $request->userAgent(),
                    0,
                    1000
                ),
                'properties' => $properties,
            ]);
        } catch (Throwable $e) {
            Log::error('AI-Q activity logging failed', [
                'action' => $action,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
