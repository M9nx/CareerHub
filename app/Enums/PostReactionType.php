<?php

namespace App\Enums;

enum PostReactionType: string
{
    case Like = 'like';

    public function label(): string
    {
        return match ($this) {
            self::Like => __('Like'),
        };
    }
}
