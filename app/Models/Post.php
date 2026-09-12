<?php

namespace App\Models;

use App\Enums\PostStatus;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'author_role',
        'title',
        'body',
        'status',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'author_role' => UserRole::class,
            'status' => PostStatus::class,
            'is_active' => 'boolean',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}