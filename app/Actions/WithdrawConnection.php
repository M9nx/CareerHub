<?php

namespace App\Actions;

use App\Enums\ConnectionStatus;
use App\Models\Connection;

class WithdrawConnection
{
    public function handle(Connection $connection): Connection
    {
        $connection->update([
            'status' => ConnectionStatus::Withdrawn,
        ]);

        return $connection->fresh(['requester', 'addressee']);
    }
}
