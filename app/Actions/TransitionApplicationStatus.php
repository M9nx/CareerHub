<?php

namespace App\Actions;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Notifications\ApplicationStatusChangedNotification;

class TransitionApplicationStatus
{
    /**
     * @return list<ApplicationStatus>
     */
    public function allowedStatuses(Application $application): array
    {
        return match ($application->status) {
            ApplicationStatus::Submitted => [ApplicationStatus::UnderReview],
            ApplicationStatus::UnderReview => [
                ApplicationStatus::Accepted,
                ApplicationStatus::Rejected,
            ],
            default => [],
        };
    }

    public function handle(
        Application $application,
        ApplicationStatus $newStatus,
    ): Application {
        abort_unless(
            in_array($newStatus, $this->allowedStatuses($application), true),
            403
        );

        $application->update([
            'status' => $newStatus,
        ]);

        $application->loadMissing(['employee', 'jobPosting']);

        $application->employee->notify(
            new ApplicationStatusChangedNotification($application)
        );

        return $application;
    }
}
