<?php

namespace App\Enums;

enum ConnectionStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Withdrawn = 'withdrawn';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Accepted => 'Connected',
            self::Rejected => 'Ignored',
            self::Withdrawn => 'Withdrawn',
        };
    }
}
