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
            ->columns([
                TextColumn::make('title')
                    ->searchable(),

                TextColumn::make('employer.name')
                    ->label('Employer')
                    ->searchable(),

                TextColumn::make('status')
                    ->badge(),
            ])
            ->Actions([
                ViewAction::make(),

                Action::make('forceClose')
                    ->label('Force Close')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (JobPosting $record): bool => $record->status !== JobPostingStatus::Closed)
                    ->action(function (JobPosting $record): void {
                        $record->update(['status' => JobPostingStatus::Closed]);

                        Notification::make()
                            ->title('Job posting closed successfully')
                            ->success()
                            ->send();
                    }),

                Action::make('archive')
                    ->label('Archive')
                    ->icon('heroicon-o-archive-box')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (JobPosting $record): bool => $record->status !== JobPostingStatus::Archived)
                    ->action(function (JobPosting $record): void {
                        $record->update(['status' => JobPostingStatus::Archived]);

                        Notification::make()
                            ->title('Job posting archived successfully')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageJobPostings::route('/'),
        ];
    }
}