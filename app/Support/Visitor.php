<?php

namespace App\Support;

use Illuminate\Support\Str;

class Visitor
{
    /** Stable key for the current visitor (user id or anonymous session id). */
    public static function key(): string
    {
        if ($id = auth()->id()) {
            return 'u:'.$id;
        }

        $vid = session('visitor_id');
        if (! $vid) {
            $vid = (string) Str::uuid();
            session(['visitor_id' => $vid]);
        }

        return 'v:'.$vid;
    }

    public static function nickname(): ?string
    {
        return auth()->user()?->name ?? session('guest_nickname');
    }

    public static function rememberNickname(string $name): void
    {
        session(['guest_nickname' => $name]);
    }
}
