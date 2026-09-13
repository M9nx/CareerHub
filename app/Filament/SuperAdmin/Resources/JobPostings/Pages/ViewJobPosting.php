<?php

namespace App\Filament\SuperAdmin\Resources\JobPostings\Pages;

use App\Filament\SuperAdmin\Resources\JobPostings\JobPostingResource;
use Filament\Resources\Pages\ViewRecord;

class ViewJobPosting extends ViewRecord
{
    protected static string $resource = JobPostingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            JobPostingResource::forceCloseAction(),
            JobPostingResource::archiveAction(),
        ];
    }
}
