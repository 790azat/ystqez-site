<?php

namespace App\Support;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class Locale
{
    /** Supported locales => short switcher label. First one is the default. */
    public const SUPPORTED = [
        'hy' => 'ՀԱՅ',
        'ru' => 'РУС',
        'en' => 'ENG',
    ];

    public const NAMES = [
        'hy' => 'Հայերեն',
        'ru' => 'Русский',
        'en' => 'English',
    ];

    /** Date formats per locale (Carbon::translatedFormat). */
    public const DATE_FORMATS = [
        'hy' => ['short' => 'j M Y', 'long' => 'j F Y', 'datetime' => 'j F Y, H:i', 'short_datetime' => 'j M Y, H:i', 'numeric' => 'd.m.Y'],
        'ru' => ['short' => 'j M Y', 'long' => 'j F Y', 'datetime' => 'j F Y, H:i', 'short_datetime' => 'j M Y, H:i', 'numeric' => 'd.m.Y'],
        'en' => ['short' => 'M j, Y', 'long' => 'F j, Y', 'datetime' => 'F j, Y, H:i', 'short_datetime' => 'M j, Y, H:i', 'numeric' => 'Y-m-d'],
    ];

    public static function isSupported(?string $locale): bool
    {
        return is_string($locale) && array_key_exists($locale, self::SUPPORTED);
    }

    public static function default(): string
    {
        $configured = config('app.locale');

        return self::isSupported($configured) ? $configured : array_key_first(self::SUPPORTED);
    }

    public static function current(): string
    {
        $locale = app()->getLocale();

        return self::isSupported($locale) ? $locale : self::default();
    }

    public static function apply(string $locale): void
    {
        app()->setLocale($locale);
        Carbon::setLocale($locale);
        \Carbon\Carbon::setLocale($locale);
        \Carbon\CarbonImmutable::setLocale($locale);
    }

    public static function date(?CarbonInterface $date, string $style = 'long'): string
    {
        if (! $date) {
            return '';
        }
        $formats = self::DATE_FORMATS[self::current()] ?? self::DATE_FORMATS['ru'];

        return $date->copy()->locale(self::current())->translatedFormat($formats[$style] ?? $style);
    }

    /** Integer with locale thousands separator. */
    public static function number(int|float|null $n, int $decimals = 0): string
    {
        $n = (float) ($n ?? 0);

        return self::current() === 'en'
            ? number_format($n, $decimals, '.', ',')
            : number_format($n, $decimals, ',', ' ');
    }

    /** Compact number: 12,5 тыс. / 12.5K / 12,5 հզ. */
    public static function compact(int|float|null $n): string
    {
        $n = (float) ($n ?? 0);
        $dec = self::current() === 'en' ? '.' : ',';
        $short = fn (float $v) => rtrim(rtrim(number_format($v, 1, $dec, ''), '0'), $dec);

        if ($n >= 1_000_000) {
            return __(':n млн', ['n' => $short($n / 1_000_000)]);
        }
        if ($n >= 1_000 && $n < 10_000) {
            return self::number($n);
        }
        if ($n >= 1_000) {
            return __(':n тыс.', ['n' => $short($n / 1_000)]);
        }

        return (string) (int) $n;
    }
}
