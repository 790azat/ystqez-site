<?php

namespace App\Http\Middleware;

use App\Support\Locale;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = null;
        foreach ([
            $request->hasSession() ? $request->session()->get('locale') : null,
            $request->cookie('locale'),
        ] as $candidate) {
            if (Locale::isSupported($candidate)) {
                $locale = $candidate;
                break;
            }
        }

        Locale::apply($locale ?? Locale::default());

        return $next($request);
    }
}
