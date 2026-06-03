<?php

namespace App\Enums;

enum Sport: string
{
    case Cricket = 'Cricket';
    case Football = 'Football';

    public static function validationRule(): string
    {
        return 'in:' . implode(',', array_column(self::cases(), 'value'));
    }
}
