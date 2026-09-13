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
                ->visible(fn (User $record): bool => $record->isActive() && $record->isNot(auth()->user()))
                ->requiresConfirmation()
                ->action(function (User $record): void {
                    abort_if($record->is(auth()->user()), 403);
                    $record->update(['is_active' => false]);
                }),

            Action::make('activate')
                ->label('Activate')
                ->color('success')
                ->visible(fn (User $record): bool => ! $record->isActive() && $record->isNot(auth()->user()))
                ->requiresConfirmation()
                ->action(function (User $record): void {
                    abort_if($record->is(auth()->user()), 403);
                    $record->update(['is_active' => true]);
                }),

            DeleteAction::make()
                ->hidden(fn (User $record): bool => $record->is(auth()->user()))
                ->before(fn (User $record) => abort_if($record->is(auth()->user()), 403)),
        ];
    }
}
