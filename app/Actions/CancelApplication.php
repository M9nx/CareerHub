<?php

namespace App\Actions;

use App\Enums\ApplicationStatus;
use App\Models\Application;

class CancelApplication
{
    public function handle(Application $application): Application
    {
        abort_if(
            in_array($application->status, [
                ApplicationStatus::Accepted,
                ApplicationStatus::Rejected,
                ApplicationStatus::Cancelled,
            ], true),
            403
        );

        $application->update([
            'status' => ApplicationStatus::Cancelled,
            'cancelled_at' => now(),
        ]);

        return $application;
    }
}
