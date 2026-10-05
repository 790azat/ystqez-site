<?php

use App\Support\Settings;

if (! function_exists('setting')) {
    function setting(string $key, ?string $default = null): ?string
    {
        return Settings::get($key, $default);
    }
}

if (! function_exists('media_url')) {
    /**
     * URL for a local media file (public/media/...). Absolute URLs are returned as-is.
     * Uses config('media.cdn_url') when set, otherwise the app's own asset() URL.
     */
    function media_url(?string $path): ?string
    {
        if ($path === null || trim($path) === '') {
            return null;
        }
        $path = trim($path);
        if (preg_match('~^(https?:)?//~i', $path) || str_starts_with($path, 'data:')) {
            return $path;
        }
        $path = ltrim($path, '/');
        if (str_starts_with($path, 'public/')) {
            $path = substr($path, 7);
        }
        if (! str_starts_with($path, 'media/')) {
            $path = 'media/'.$path;
        }
        $cdn = rtrim((string) config('media.cdn_url'), '/');

        return $cdn !== '' ? $cdn.'/'.$path : asset($path);
    }
}

if (! function_exists('fdate')) {
    /** Localized date: styles short | long | datetime | short_datetime | numeric. */
    function fdate(?\Carbon\CarbonInterface $date, string $style = 'long'): string
    {
        return \App\Support\Locale::date($date, $style);
    }
}

if (! function_exists('num')) {
    function num(int|float|null $n): string
    {
        return \App\Support\Locale::number($n);
    }
}

if (! function_exists('compact_num')) {
    function compact_num(int|float|null $n): string
    {
        return \App\Support\Locale::compact($n);
    }
}
