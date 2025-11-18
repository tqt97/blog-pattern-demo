<?php

namespace App\Enums;

enum CommentStatus: string
{
    case APPROVED = 'approved';
    case PENDING = 'pending';
    case SPAM = 'spam';

    public function label(): string
    {
        return match ($this) {
            self::APPROVED => 'Approved',
            self::PENDING => 'Pending',
            self::SPAM => 'Spam',
        };
    }
}
