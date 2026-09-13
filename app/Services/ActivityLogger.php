<?php

namespace App\Services;

use App\Enums\JobPostingStatus;
use App\Models\ActivityLog;
use App\Models\JobPosting;
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

    /**
     * @param  array<string, mixed>  $props
     */
    public function logJobPostingEvent(string $event, JobPosting $jobPosting, ?User $causer = null, array $props = []): ActivityLog
    {
        $status = $jobPosting->status instanceof JobPostingStatus
            ? $jobPosting->status->value
            : $jobPosting->status;

        return $this->log('job_posting.'.$event, $jobPosting, $causer, [
            'event' => $event,
            'status' => $status,
            'title' => $jobPosting->title,
            'employer_id' => $jobPosting->employer_id,
            ...$props,
        ]);
    }
}
