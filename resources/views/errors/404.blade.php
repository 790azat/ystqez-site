<x-layouts.app title="{{ __('Страница не найдена') }}">
<div class="container-yq grid min-h-[60vh] place-items-center py-16 text-center">
    <div>
        <p class="font-display text-8xl font-semibold gradient-text">404</p>
        <h1 class="mt-4 text-2xl font-semibold">{{ __('Страница не найдена') }}</h1>
        <p class="mt-3 text-ink-400">{{ __('Похоже, этот разговор ещё не записан. Или ссылка устарела.') }}</p>
        <a href="{{ route('home') }}" class="btn btn-primary mt-8">{{ __('На главную') }}</a>
    </div>
</div>
</x-layouts.app>
