<?php

namespace App\Models;

use Database\Factories\ProfileExperienceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileExperience extends Model
{
    /** @use HasFactory<ProfileExperienceFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'company',
        'location',
        'started_at',
        'ended_at',
        'description',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'started_at' => 'date',
            'ended_at' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
