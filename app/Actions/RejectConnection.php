<?php

namespace App\Actions;

use App\Enums\ConnectionStatus;
use App\Models\Connection;

class RejectConnection
{
    public function handle(Connection $connection): Connection
    {
        $connection->update([
            'status' => ConnectionStatus::Rejected,
        ]);

        return $connection->fresh(['requester', 'addressee']);
    }
}
