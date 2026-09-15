<?php

namespace App\Models;

use App\Enums\ConnectionStatus;
use Database\Factories\ConnectionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Connection extends Model
{
    /** @use HasFactory<ConnectionFactory> */
    use HasFactory;

    protected $fillable = [
        'requester_id',
        'addressee_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => ConnectionStatus::class,
        ];
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function addressee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'addressee_id');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ConnectionStatus::Pending);
    }

    public function scopeAccepted(Builder $query): Builder
    {
        return $query->where('status', ConnectionStatus::Accepted);
    }

    public function involves(User $user): bool
    {
        return (int) $this->requester_id === (int) $user->id
            || (int) $this->addressee_id === (int) $user->id;
    }

    public function otherParty(User $user): User
    {
        return (int) $this->requester_id === (int) $user->id
            ? $this->addressee
            : $this->requester;
    }

    public static function between(User $a, User $b): ?self
    {
        return static::query()
            ->where(function (Builder $query) use ($a, $b): void {
                $query->where(function (Builder $inner) use ($a, $b): void {
                    $inner->where('requester_id', $a->id)->where('addressee_id', $b->id);
                })->orWhere(function (Builder $inner) use ($a, $b): void {
                    $inner->where('requester_id', $b->id)->where('addressee_id', $a->id);
                });
            })
            ->latest('id')
            ->first();
    }

    public static function hasOpenPair(User $a, User $b): bool
    {
        return static::query()
            ->whereIn('status', [ConnectionStatus::Pending, ConnectionStatus::Accepted])
            ->where(function (Builder $query) use ($a, $b): void {
                $query->where(function (Builder $inner) use ($a, $b): void {
                    $inner->where('requester_id', $a->id)->where('addressee_id', $b->id);
                })->orWhere(function (Builder $inner) use ($a, $b): void {
                    $inner->where('requester_id', $b->id)->where('addressee_id', $a->id);
                });
            })
            ->exists();
    }
}
