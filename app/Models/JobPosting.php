<?php

namespace App\Models;

use App\Enums\JobPostingStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobPosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'employer_id',
        'title',
        'description',
        'status',
        'is_active',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => JobPostingStatus::class,
            'is_active' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function scopePublished(Builder $query): void
    {
        $query
            ->where('status', JobPostingStatus::Published)
            ->where('is_active', true);
    }
}
