<?php

namespace App\Observers;

use App\Enums\JobPostingStatus;
use App\Models\JobPosting;
use App\Models\User;
use App\Services\ActivityLogger;

class JobPostingObserver
{
    public function __construct(private ActivityLogger $activityLogger) {}

    public function created(JobPosting $jobPosting): void
    {
        $this->activityLogger->logJobPostingEvent('created', $jobPosting, $this->causer());

        $this->logLifecycleEvent($jobPosting);
    }

    public function updated(JobPosting $jobPosting): void
    {
        if (! $jobPosting->wasChanged('status')) {
            return;
        }

        $this->logLifecycleEvent($jobPosting, $this->statusValue($jobPosting->getOriginal('status')));
    }

    private function logLifecycleEvent(JobPosting $jobPosting, ?string $fromStatus = null): void
    {
        $event = match ($jobPosting->status) {
            JobPostingStatus::Published => 'published',
            JobPostingStatus::Closed => 'closed',
            default => null,
        };

        if ($event === null) {
            return;
        }

        $this->activityLogger->logJobPostingEvent(
            $event,
            $jobPosting,
            $this->causer(),
            array_filter(['from_status' => $fromStatus], fn (mixed $value): bool => $value !== null),
        );
    }

    private function causer(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }

    private function statusValue(mixed $status): ?string
    {
        if ($status instanceof JobPostingStatus) {
            return $status->value;
        }

        return is_string($status) ? $status : null;
    }
}
