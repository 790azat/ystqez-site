<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/*
| Serverless (Vercel) support: the filesystem is read-only except /tmp.
| Make sure compiled views directory exists and move storage to /tmp.
*/
$viewPath = $_ENV['VIEW_COMPILED_PATH'] ?? $_SERVER['VIEW_COMPILED_PATH'] ?? getenv('VIEW_COMPILED_PATH') ?: null;
if ($viewPath && ! is_dir($viewPath)) {
    @mkdir($viewPath, 0755, true);
}

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        // The locale cookie is not sensitive: keep it plain so it survives key rotation.
        $middleware->encryptCookies(except: ['locale']);
        // Global too, so error pages for unmatched routes (404) are localized from the cookie.
        $middleware->append(\App\Http\Middleware\SetLocale::class);
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

$isVercel = (bool) ($_ENV['VERCEL'] ?? $_SERVER['VERCEL'] ?? getenv('VERCEL'));
if ($isVercel) {
    $storage = '/tmp/storage';
    foreach (['framework/cache/data', 'framework/sessions', 'framework/views', 'logs', 'app/public'] as $dir) {
        if (! is_dir("$storage/$dir")) {
            @mkdir("$storage/$dir", 0755, true);
        }
    }
    $app->useStoragePath($storage);
}

return $app;
