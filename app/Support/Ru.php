<?php

namespace App\Support;

class Ru
{
    /** @param array{0:string,1:string,2:string} $forms */
    public static function plural(int $n, array $forms): string
    {
        $n = abs($n) % 100;
        $n1 = $n % 10;
        if ($n > 10 && $n < 20) {
            return $forms[2];
        }
        if ($n1 > 1 && $n1 < 5) {
            return $forms[1];
        }
        if ($n1 === 1) {
            return $forms[0];
        }

        return $forms[2];
    }

    public static function compact(int|float|null $n): string
    {
        $n = (float) ($n ?? 0);
        if ($n >= 1_000_000) {
            return rtrim(rtrim(number_format($n / 1_000_000, 1, ',', ''), '0'), ',').' млн';
        }
        if ($n >= 1_000 && $n < 10_000) {
            return number_format($n, 0, ',', ' ');
        }
        if ($n >= 1_000) {
            return rtrim(rtrim(number_format($n / 1_000, 1, ',', ''), '0'), ',').' тыс.';
        }

        return (string) (int) $n;
    }
}
