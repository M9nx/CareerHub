<?php

namespace App\Policies;

use App\Enums\ApplicationStatus;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    public function create(User $user): bool
    {
        return $user->role === UserRole::Employee;
    }

    public function view(User $user, Application $application): bool
    {
        return $user->role === UserRole::Employee
            && $application->employee_id === $user->id;
    }

    public function cancel(User $user, Application $application): bool
    {
        return $user->role === UserRole::Employee
            && $application->employee_id === $user->id
            && ! in_array($application->status, [
                ApplicationStatus::Accepted,
                ApplicationStatus::Rejected,
            ], true);
    }

    public function update(User $user, Application $application): bool
    {
        return $user->role === UserRole::Employer
            && $application->jobPosting->employer_id === $user->id;
    }
}
