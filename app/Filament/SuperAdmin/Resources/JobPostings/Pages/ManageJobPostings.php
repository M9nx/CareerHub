<?php

namespace App\Filament\SuperAdmin\Resources\JobPostings\Pages;

use App\Filament\SuperAdmin\Resources\JobPostings\JobPostingResource;
use Filament\Resources\Pages\ManageRecords;

class ManageJobPostings extends ManageRecords
{
    protected static string $resource = JobPostingResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
