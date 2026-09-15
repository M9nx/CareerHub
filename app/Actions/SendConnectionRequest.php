<?php

namespace App\Actions;

use App\Enums\ConnectionStatus;
use App\Enums\UserRole;
use App\Models\Connection;
use App\Models\User;
use App\Notifications\ConnectionRequestReceivedNotification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

class SendConnectionRequest
{
    public function handle(User $requester, User $addressee): Connection
    {
        if ((int) $requester->id === (int) $addressee->id) {
            throw ValidationException::withMessages([
                'user' => __('You cannot connect with yourself.'),
            ]);
        }

        if (! $this->isNetworkPersona($addressee) || ! $addressee->isActive()) {
            throw new AuthorizationException;
        }

        if (Connection::hasOpenPair($requester, $addressee)) {
            throw ValidationException::withMessages([
                'user' => __('A connection already exists with this person.'),
            ]);
        }

        $existing = Connection::between($requester, $addressee);

        if ($existing !== null) {
            $existing->update([
                'requester_id' => $requester->id,
                'addressee_id' => $addressee->id,
                'status' => ConnectionStatus::Pending,
            ]);

            $connection = $existing->fresh(['requester', 'addressee']);
        } else {
            $connection = Connection::create([
                'requester_id' => $requester->id,
                'addressee_id' => $addressee->id,
                'status' => ConnectionStatus::Pending,
            ])->load(['requester', 'addressee']);
        }

        $addressee->notify(new ConnectionRequestReceivedNotification($connection));

        return $connection;
    }

    private function isNetworkPersona(User $user): bool
    {
        return in_array($user->role, [UserRole::Employee, UserRole::Employer], true);
    }
}
