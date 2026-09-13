<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    /**
     * @param  array<string, mixed>  $props
     */
    public function log(string $name, Model $subject, ?User $causer = null, array $props = []): ActivityLog
    {
        return ActivityLog::query()->create([
            'log_name' => $name,
            'description' => $name,
            'subject_type' => $subject->getMorphClass(),
            'subject_id' => $subject->getKey(),
            'causer_id' => $causer?->getKey(),
            'properties' => $props,
        ]);
    }
}
