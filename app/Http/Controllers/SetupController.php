<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Throwable;

class SetupController extends Controller
{
    public function __invoke(string $token)
    {
        $expected = (string) config('site.setup_token');
        abort_if($expected === '' || ! hash_equals($expected, $token), 404);

        @set_time_limit(120);
        $log = [];
        try {
            Artisan::call('migrate', ['--force' => true]);
            $log[] = trim(Artisan::output());
            Artisan::call('db:seed', ['--force' => true]);
            $log[] = trim(Artisan::output());
            $status = 200;
            $log[] = 'Готово ✔';
        } catch (Throwable $e) {
            $status = 500;
            $log[] = 'Ошибка: '.$e->getMessage();
        }

        return response(implode("\n\n", $log), $status)
            ->header('Content-Type', 'text/plain; charset=utf-8')
            ->header('X-Robots-Tag', 'noindex');
    }
}
