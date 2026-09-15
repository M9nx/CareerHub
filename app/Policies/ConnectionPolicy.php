<?php

namespace App\Policies;

use App\Enums\ConnectionStatus;
use App\Enums\UserRole;
use App\Models\Connection;
use App\Models\User;

class ConnectionPolicy
{
    public function create(User $user): bool
    {
        return $this->isNetworkPersona($user) && $user->isActive();
    }

    public function accept(User $user, Connection $connection): bool
    {
        return $connection->status === ConnectionStatus::Pending
            && (int) $connection->addressee_id === (int) $user->id
            && $user->isActive();
    }

    public function reject(User $user, Connection $connection): bool
    {
        return $this->accept($user, $connection);
    }

    public function withdraw(User $user, Connection $connection): bool
    {
        return $connection->status === ConnectionStatus::Pending
            && (int) $connection->requester_id === (int) $user->id
            && $user->isActive();
    }

    public function delete(User $user, Connection $connection): bool
    {
        return $connection->status === ConnectionStatus::Accepted
            && $connection->involves($user)
            && $user->isActive();
    }

    private function isNetworkPersona(User $user): bool
    {
        return in_array($user->role, [UserRole::Employee, UserRole::Employer], true);
    }
}
