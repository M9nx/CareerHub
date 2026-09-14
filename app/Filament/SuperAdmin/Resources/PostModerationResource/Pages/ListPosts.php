<?php

namespace App\Filament\SuperAdmin\Resources\PostModerationResource\Pages;

use App\Filament\SuperAdmin\Resources\PostModerationResource;
use Filament\Resources\Pages\ListRecords;

class ListPosts extends ListRecords
{
    protected static string $resource = PostModerationResource::class;
}
