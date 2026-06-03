<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Pending = 'Pending';
    case Confirmed = 'Confirmed';
    case Paid = 'Paid';
    case Cancelled = 'Cancelled';

    public static function validationRule(): string
    {
        return 'in:' . implode(',', array_column(self::cases(), 'value'));
    }
}
