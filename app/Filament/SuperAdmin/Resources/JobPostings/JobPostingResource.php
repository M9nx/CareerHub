<?php

namespace App\Filament\SuperAdmin\Resources\JobPostings;

use App\Enums\JobPostingStatus;
use App\Models\JobPosting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JobPostingResource extends Resource
{
    protected static ?string $model = JobPosting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),
                TextEntry::make('employer.name')
                    ->label('Employer'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->modifyQueryUsing(fn ($query) => $query->with('employer'))
            ->columns([
                TextColumn::make('title')
                    ->searchable(),

                TextColumn::make('employer.name')
                    ->label('Employer')
                    ->searchable(),

                TextColumn::make('status')
                    ->badge(),
            ])
            ->recordActions([
                ViewAction::make(),
                static::forceCloseAction(),
                static::archiveAction(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageJobPostings::route('/'),
            'view' => Pages\ViewJobPosting::route('/{record}'),
        ];
    }

    public static function forceCloseAction(): Action
    {
        return Action::make('forceClose')
            ->label('Force Close')
            ->icon(Heroicon::OutlinedXCircle)
            ->color('danger')
            ->requiresConfirmation()
            ->visible(fn (JobPosting $record): bool => $record->status !== JobPostingStatus::Closed)
            ->action(function (JobPosting $record): void {
                $record->update(['status' => JobPostingStatus::Closed]);

                Notification::make()
                    ->title(__('Job posting closed successfully'))
                    ->success()
                    ->send();
            });
    }

    public static function archiveAction(): Action
    {
        return Action::make('archive')
            ->label('Archive')
            ->icon(Heroicon::OutlinedArchiveBox)
            ->color('warning')
            ->requiresConfirmation()
            ->visible(fn (JobPosting $record): bool => $record->status !== JobPostingStatus::Archived)
            ->action(function (JobPosting $record): void {
                $record->update(['status' => JobPostingStatus::Archived]);

                Notification::make()
                    ->title(__('Job posting archived successfully'))
                    ->success()
                    ->send();
            });
    }
}
