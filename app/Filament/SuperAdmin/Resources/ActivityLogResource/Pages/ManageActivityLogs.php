<?php

namespace App\Filament\SuperAdmin\Resources\ActivityLogResource\Pages;

use App\Filament\SuperAdmin\Resources\ActivityLogResource;
use Filament\Resources\Pages\ManageRecords;

class ManageActivityLogs extends ManageRecords
{
    protected static string $resource = ActivityLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
