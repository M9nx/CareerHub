<?php

namespace App\Actions;

use App\Models\Connection;

class RemoveConnection
{
    public function handle(Connection $connection): void
    {
        $connection->delete();
    }
}
