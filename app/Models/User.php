<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable; // <-- 1. Added import for 2FA

#[Fillable(['name', 'email', 'password', 'role', 'is_active', 'is_blocked_from_posts'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser // <-- 2. Added 'implements FilamentUser'
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable; // <-- 3. Added the 2FA trait here

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
            'is_blocked_from_posts' => 'boolean',

        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === UserRole::SuperAdmin
            && $panel->getId() === 'super-admin';
    }

    /**
     * Whether the user holds the SuperAdmin role.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }

    /**
     * Whether the user's account is active.
     */
    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /**
     * Whether the user is currently blocked from creating posts.
     */
    public function isBlockedFromPosts(): bool
    {
        return (bool) $this->is_blocked_from_posts;
    }
}