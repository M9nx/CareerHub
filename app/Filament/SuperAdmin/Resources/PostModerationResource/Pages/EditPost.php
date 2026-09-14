<?php

namespace App\Filament\SuperAdmin\Resources\PostModerationResource\Pages;

use App\Filament\SuperAdmin\Resources\PostModerationResource;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostModerationResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return static::getResource()::canAccess();
    }
}
