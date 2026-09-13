<?php

namespace App\Filament\SuperAdmin\Resources\UserResource\Pages;

use App\Enums\UserRole;
use App\Filament\SuperAdmin\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * Users created through this panel default to the SuperAdmin role,
     * per the file contract ("Role SuperAdmin"), unless a different role
     * was explicitly picked in the form.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['role'] ??= UserRole::SuperAdmin->value;

        return $data;
    }
}