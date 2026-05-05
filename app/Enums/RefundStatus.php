<?php

namespace App\Enums;

enum RefundStatus: int
{
    case Requested = 0;
    case Approved = 1;
    case Rejected = 2;
    case ItemReceived = 3;
    case Refunded = 4;

    public function label(): string
    {
        return match ($this) {
            self::Requested => 'Requested',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::ItemReceived => 'Item Received',
            self::Refunded => 'Refunded',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Requested => 'warning',
            self::Approved => 'info',
            self::Rejected => 'danger',
            self::ItemReceived => 'primary',
            self::Refunded => 'success',
        };
    }
}
