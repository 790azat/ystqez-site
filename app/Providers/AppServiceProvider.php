<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        \App\Support\Locale::apply(\App\Support\Locale::default());

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        \Livewire\Livewire::addPersistentMiddleware([\App\Http\Middleware\EnsureAdmin::class, \App\Http\Middleware\SetLocale::class]);

        Paginator::defaultView('partials.pagination');
        Paginator::defaultSimpleView('partials.pagination');
    }
}
