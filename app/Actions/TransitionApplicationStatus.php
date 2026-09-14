<?php

namespace App\Actions;

use App\Enums\ApplicationStatus;
use App\Models\Application;

class TransitionApplicationStatus
{
    /**
     * @return Application
     */
    public function handle(
        Application $application,
        ApplicationStatus $newStatus,
    ): Application {
        $allowedTransitions = [
            ApplicationStatus::Submitted->value => [
                ApplicationStatus::UnderReview,
            ],

            ApplicationStatus::UnderReview->value => [
                ApplicationStatus::Accepted,
                ApplicationStatus::Rejected,
            ],
        ];

        $allowedStatuses = $allowedTransitions[
            $application->status->value
        ] ?? [];

        abort_unless(
            in_array($newStatus, $allowedStatuses, true),
            403
        );

        $application->update([
            'status' => $newStatus,
        ]);

        return $application->fresh();
    }
}