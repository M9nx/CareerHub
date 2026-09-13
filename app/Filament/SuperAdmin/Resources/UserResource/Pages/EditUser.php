<?php

namespace App\Filament\SuperAdmin\Resources\UserResource\Pages;

use App\Filament\SuperAdmin\Resources\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('deactivate')
                ->label('Deactivate')
                ->color('danger')
                ->visible(fn (User $record): bool => $record->isActive())
                ->requiresConfirmation()
                ->action(fn (User $record) => $record->update(['is_active' => false])),

            Action::make('activate')
                ->label('Activate')
                ->color('success')
                ->visible(fn (User $record): bool => ! $record->isActive())
                ->requiresConfirmation()
                ->action(fn (User $record) => $record->update(['is_active' => true])),

            DeleteAction::make(),
        ];
    }
}