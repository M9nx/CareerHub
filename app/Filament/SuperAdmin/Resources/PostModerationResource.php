<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Enums\PostStatus;
use App\Filament\SuperAdmin\Resources\PostModerationResource\Pages;
use App\Models\Post;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PostModerationResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

    protected static ?string $navigationLabel = 'Post Moderation';

    protected static ?string $recordTitleAttribute = 'title';

    // Set explicitly instead of relying on Filament's auto-pluralized
    // default slug, so the URL is predictable: /super-admin/post-moderation
    protected static ?string $slug = 'post-moderation';

    /**
     * Builds ['value' => 'Label'] options from the PostStatus enum manually,
     * matching the same pattern used for UserRole in UserResource.
     */
    protected static function statusOptions(): array
    {
        return collect(PostStatus::cases())
            ->mapWithKeys(fn (PostStatus $status) => [
                $status->value => str($status->name)->headline()->toString(),
            ])
            ->all();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('body')
                    ->required()
                    ->rows(6)
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(static::statusOptions())
                    ->required()
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('author')->latest('created_at')->orderByDesc('id'))
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('author.name')
                    ->label('Author')
                    ->searchable(),
                TextColumn::make('author_role')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label() ?? (string) $state),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (PostStatus $state): string => match ($state) {
                        PostStatus::Published => 'success',
                        PostStatus::Draft => 'gray',
                        PostStatus::Hidden => 'warning',
                        PostStatus::Archived => 'danger',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(static::statusOptions()),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('hide')
                    ->label('Hide')
                    ->icon(Heroicon::OutlinedEyeSlash)
                    ->color('warning')
                    ->visible(fn (Post $record): bool => $record->status !== PostStatus::Hidden)
                    ->requiresConfirmation()
                    ->successNotificationTitle('Post hidden')
                    ->action(fn (Post $record) => $record->update(['status' => PostStatus::Hidden])),
                Action::make('archive')
                    ->label('Archive')
                    ->icon(Heroicon::OutlinedArchiveBox)
                    ->color('danger')
                    ->visible(fn (Post $record): bool => $record->status !== PostStatus::Archived)
                    ->requiresConfirmation()
                    ->successNotificationTitle('Post archived')
                    ->action(fn (Post $record) => $record->update(['status' => PostStatus::Archived])),
                // Links out to the author's UserResource edit page, where the
                // blockFromPosts / unblockFromPosts action (issue #39) lives.
                Action::make('blockAuthor')
                    ->label('Block author from posts')
                    ->icon(Heroicon::OutlinedNoSymbol)
                    ->color('gray')
                    ->visible(fn (Post $record): bool => $record->author !== null)
                    ->url(fn (Post $record): string => UserResource::getUrl('edit', ['record' => $record->author]))
                    ->openUrlInNewTab(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
