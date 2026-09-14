<?php

namespace App\Filament\SuperAdmin\Resources\ApplicationResource\Pages;

use App\Filament\SuperAdmin\Resources\ApplicationResource;
use Filament\Resources\Pages\ListRecords;

class ListApplications extends ListRecords
{
    protected static string $resource = ApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
