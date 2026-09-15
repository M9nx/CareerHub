<?php

namespace App\Actions;

use App\Enums\ConnectionStatus;
use App\Models\Connection;

class AcceptConnection
{
    public function handle(Connection $connection): Connection
    {
        $connection->update([
            'status' => ConnectionStatus::Accepted,
        ]);

        return $connection->fresh(['requester', 'addressee']);
    }
}
