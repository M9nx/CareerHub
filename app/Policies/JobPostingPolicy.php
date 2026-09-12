<?php

namespace App\Policies;

use App\Enums\JobPostingStatus;
use App\Enums\UserRole;
use App\Models\JobPosting;
use App\Models\User;

class JobPostingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::SuperAdmin
            || $user->role === UserRole::Employer;
    }

    public function view(User $user, JobPosting $jobPosting): bool
    {
        if ($user->role === UserRole::SuperAdmin) {
            return true;
        }

        if ($user->role === UserRole::Employer) {
            return $jobPosting->employer_id === $user->id;
        }

        return $jobPosting->status === JobPostingStatus::Published
            && $jobPosting->is_active;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::Employer;
    }

    public function update(User $user, JobPosting $jobPosting): bool
    {
        return $user->role === UserRole::Employer
            && $jobPosting->employer_id === $user->id;
    }

    public function delete(User $user, JobPosting $jobPosting): bool
    {
        return $user->role === UserRole::Employer
            && $jobPosting->employer_id === $user->id;
    }
}
