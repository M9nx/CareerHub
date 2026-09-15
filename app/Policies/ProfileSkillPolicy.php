<?php

namespace App\Policies;

use App\Models\ProfileSkill;
use App\Models\User;

class ProfileSkillPolicy
{
    public function create(User $user): bool
    {
        return $user->isActive() && ! $user->isSuperAdmin();
    }

    public function update(User $user, ProfileSkill $profileSkill): bool
    {
        return $profileSkill->user_id === $user->id;
    }

    public function delete(User $user, ProfileSkill $profileSkill): bool
    {
        return $profileSkill->user_id === $user->id;
    }
}
