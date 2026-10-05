<?php

namespace App\Http\Controllers;

use App\Support\Locale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        abort_unless(Locale::isSupported($locale), 404);

        $request->session()->put('locale', $locale);
        Locale::apply($locale);

        $back = url()->previous();
        $host = parse_url(url('/'), PHP_URL_HOST);
        if (! $back || parse_url($back, PHP_URL_HOST) !== $host || str_contains($back, '/lang/') || str_contains($back, '/livewire')) {
            $back = route('home');
        }

        return redirect()->to($back)->withCookie(cookie('locale', $locale, 60 * 24 * 365));
    }
}
