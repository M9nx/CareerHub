<?php

namespace App\Actions;

use App\Enums\ConnectionStatus;
use App\Models\Connection;
use App\Notifications\ConnectionAcceptedNotification;

class AcceptConnection
{
    public function handle(Connection $connection): Connection
    {
        $connection->update([
            'status' => ConnectionStatus::Accepted,
        ]);

        $connection = $connection->fresh(['requester', 'addressee']);

        $connection->requester->notify(new ConnectionAcceptedNotification($connection));

        return $connection;
    }
}
