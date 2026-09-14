<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Actions\BlockUserFromPosts;
use App\Enums\UserRole;
use App\Filament\SuperAdmin\Resources\UserResource\Pages;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Builds ['value' => 'Label'] options from the UserRole enum manually,
     * so this doesn't depend on whether UserRole implements Filament's
     * HasLabel interface.
     */
    protected static function roleOptions(): array
    {
        return collect(UserRole::cases())
            ->mapWithKeys(fn (UserRole $role) => [
                $role->value => str($role->name)->headline()->toString(),
            ])
            ->all();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('password')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->maxLength(255),

                Select::make('role')
                    ->options(static::roleOptions())
                    ->required()
                    ->native(false)
                    ->disabled(fn (?User $record): bool => static::isCurrentUser($record))
                    ->dehydrated(fn (?User $record): bool => ! static::isCurrentUser($record)),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->disabled(fn (?User $record): bool => static::isCurrentUser($record))
                    ->dehydrated(fn (?User $record): bool => ! static::isCurrentUser($record)),

                Toggle::make('is_blocked_from_posts')
                    ->label('Blocked from posting')
                    ->helperText('Prefer the table Block/Unblock from posts actions so moderation is logged.')
                    ->default(false)
                    ->disabled()
                    ->dehydrated(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('email')
                    ->searchable(),

                TextColumn::make('role')
                    ->badge(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                IconColumn::make('is_blocked_from_posts')
                    ->label('Blocked')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),

                Action::make('activate')
                    ->label('Activate')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (User $record): bool => ! $record->isActive() && ! static::isCurrentUser($record))
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        abort_if(static::isCurrentUser($record), 403);
                        $record->update(['is_active' => true]);
                    }),

                Action::make('deactivate')
                    ->label('Deactivate')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->visible(fn (User $record): bool => $record->isActive() && ! static::isCurrentUser($record))
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        abort_if(static::isCurrentUser($record), 403);
                        $record->update(['is_active' => false]);
                    }),

                Action::make('blockFromPosts')
                    ->label('Block from posts')
                    ->icon(Heroicon::OutlinedNoSymbol)
                    ->color('danger')
                    ->visible(fn (User $record): bool => ! $record->isBlockedFromPosts() && ! static::isCurrentUser($record))
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        abort_if(static::isCurrentUser($record), 403);
                        app(BlockUserFromPosts::class)->handle($record, true, auth()->user());
                    }),

                Action::make('unblockFromPosts')
                    ->label('Unblock from posts')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (User $record): bool => $record->isBlockedFromPosts() && ! static::isCurrentUser($record))
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        abort_if(static::isCurrentUser($record), 403);
                        app(BlockUserFromPosts::class)->handle($record, false, auth()->user());
                    }),

                Action::make('changeRole')
                    ->label('Change role')
                    ->icon(Heroicon::OutlinedUserCircle)
                    ->visible(fn (User $record): bool => ! static::isCurrentUser($record))
                    ->schema([
                        Select::make('role')
                            ->options(static::roleOptions())
                            ->required()
                            ->native(false),
                    ])
                    ->fillForm(fn (User $record): array => [
                        'role' => $record->role->value,
                    ])
                    ->action(function (User $record, array $data): void {
                        abort_if(static::isCurrentUser($record), 403);
                        $record->update(['role' => $data['role']]);
                    }),

                DeleteAction::make()
                    ->hidden(fn (User $record): bool => static::isCurrentUser($record))
                    ->before(fn (User $record) => abort_if(static::isCurrentUser($record), 403)),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    protected static function isCurrentUser(?User $record): bool
    {
        return $record !== null && $record->is(auth()->user());
    }
}
