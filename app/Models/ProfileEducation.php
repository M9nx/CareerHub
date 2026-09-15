<?php

namespace App\Models;

use Database\Factories\ProfileEducationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileEducation extends Model
{
    /** @use HasFactory<ProfileEducationFactory> */
    use HasFactory;

    protected $table = 'profile_educations';

    protected $fillable = [
        'user_id',
        'school',
        'degree',
        'field',
        'started_at',
        'ended_at',
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
