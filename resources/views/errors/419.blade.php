<x-layouts.app title="{{ __('Сессия истекла') }}">
<div class="container-yq grid min-h-[60vh] place-items-center py-16 text-center">
    <div>
        <p class="font-display text-8xl font-semibold gradient-text">419</p>
        <h1 class="mt-4 text-2xl font-semibold">{{ __('Сессия истекла') }}</h1>
        <p class="mt-3 text-ink-400">{{ __('Обновите страницу и попробуйте снова.') }}</p>
        <a href="{{ route('home') }}" class="btn btn-primary mt-8">{{ __('На главную') }}</a>
    </div>
</div>
</x-layouts.app>
