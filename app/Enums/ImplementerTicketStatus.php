<?php

namespace App\Enums;

enum ImplementerTicketStatus: string
{
    case OPEN = 'open';
    case PENDING_SUPPORT = 'pending_support';
    case PENDING_CLIENT = 'pending_client';
    case PENDING_RND = 'pending_rnd';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Open',
            self::PENDING_SUPPORT => 'Pending Support',
            self::PENDING_CLIENT => 'Pending Client',
            self::PENDING_RND => 'Pending R&D',
            self::CLOSED => 'Closed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::OPEN => 'info',
            self::PENDING_SUPPORT => 'warning',
            self::PENDING_CLIENT => 'danger',
            self::PENDING_RND => 'gray',
            self::CLOSED => 'success',
        };
    }
}
