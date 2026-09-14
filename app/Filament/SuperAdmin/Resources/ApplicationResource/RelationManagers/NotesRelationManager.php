<?php

namespace App\Filament\SuperAdmin\Resources\ApplicationResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class NotesRelationManager extends RelationManager
{
    protected static string $relationship = 'notes';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('body')
                    ->label('Note')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('author.name')
                    ->label('Author'),
                TextEntry::make('body')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('body')
            ->modifyQueryUsing(fn ($query) => $query->with('author'))
            ->columns([
                TextColumn::make('body')
                    ->limit(80)
                    ->wrap(),
                TextColumn::make('author.name')
                    ->label('Author'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['author_id'] = auth()->id();

                        return $data;
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
