<?php

namespace App\Policies;

use App\Models\ProfileExperience;
use App\Models\User;

class ProfileExperiencePolicy
{
    public function create(User $user): bool
    {
        return $user->isActive() && ! $user->isSuperAdmin();
    }

    public function update(User $user, ProfileExperience $profileExperience): bool
    {
        return $profileExperience->user_id === $user->id;
    }

    public function delete(User $user, ProfileExperience $profileExperience): bool
    {
        return $profileExperience->user_id === $user->id;
    }
}
