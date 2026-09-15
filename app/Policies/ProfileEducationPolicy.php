<?php

namespace App\Policies;

use App\Models\ProfileEducation;
use App\Models\User;

class ProfileEducationPolicy
{
    public function create(User $user): bool
    {
        return $user->isActive() && ! $user->isSuperAdmin();
    }

    public function update(User $user, ProfileEducation $profileEducation): bool
    {
        return $profileEducation->user_id === $user->id;
    }

    public function delete(User $user, ProfileEducation $profileEducation): bool
    {
        return $profileEducation->user_id === $user->id;
    }
}
