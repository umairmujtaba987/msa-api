<?php

namespace App\Enums;

enum Court: string
{
    case A = 'A';
    case B = 'B';

    public static function validationRule(): string
    {
        return 'in:' . implode(',', array_column(self::cases(), 'value'));
    }
}
