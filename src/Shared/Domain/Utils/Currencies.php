<?php

declare(strict_types=1);

namespace App\Shared\Domain\Utils;

final class Currencies
{
    public static function calculateRateFromAmount(float $amount, float $taxRate): float
    {
        return self::round(
            num: ($taxRate / 100) * $amount,
        );
    }

    public static function round(float $num): float
    {
        return \round(
            num: $num,
            precision: 2,
        );
    }
}
