@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head', ['title' => $title ?? __('Админка'), 'description' => null, 'ogImage' => null])
    <meta name="robots" content="noindex">
</head>
<body class="min-h-screen">
@php
    $newCollabs = \App\Models\CollabRequest::where('status', 'new')->count();
    $items = [
        ['admin.dashboard', __('Дашборд'), 'grid', null],
        ['admin.videos', __('Видео'), 'film', null],
        ['admin.collabs', __('Заявки'), 'inbox', $newCollabs ?: null],
        ['admin.forum', __('Форум'), 'users', null],
        ['admin.chat', __('Чат'), 'chat', null],
        ['admin.comments', __('Комментарии'), 'reply', null],
        ['admin.users', __('Пользователи'), 'user', null],
        ['admin.settings', __('Настройки'), 'settings', null],
    ];
@endphp
<div class="lg:grid lg:grid-cols-[250px_minmax(0,1fr)]">
    <aside class="border-b border-white/5 bg-ink-900/80 lg:sticky lg:top-0 lg:h-screen lg:border-b-0 lg:border-r">
        <div class="flex items-center justify-between p-5">
            @include('partials.logo')
            <span class="badge bg-ember-500/15 text-ember-300">admin</span>
        </div>
        <nav class="scroll-x gap-1 px-3 pb-3 lg:grid lg:overflow-visible lg:pb-0">
            @foreach($items as [$route, $label, $icon, $badge])
                <a href="{{ route($route) }}" @class(['flex shrink-0 items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition', 'bg-white/[.07] text-white' => request()->routeIs($route), 'text-ink-400 hover:bg-white/5 hover:text-white' => !request()->routeIs($route)])>
                    <x-icon :name="$icon" class="size-4"/> {{ $label }}
                    @if($badge)<span class="ml-auto rounded-full bg-ember-500 px-2 py-0.5 text-[11px] font-bold text-ink-950">{{ $badge }}</span>@endif
                </a>
            @endforeach
            <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-ink-400 hover:bg-white/5 hover:text-white lg:mt-4">
                <x-icon name="arrow-left" class="size-4"/> {{ __('На сайт') }}
            </a>
        </nav>
    </aside>
    <main class="min-w-0 p-4 sm:p-8">
        <div class="mb-6 flex items-center justify-end gap-3 text-sm text-ink-400">
            @include('partials.lang-switch', ['class' => 'mr-auto'])
            <span class="avatar size-8 text-xs">{{ auth()->user()->initials() }}</span> {{ auth()->user()->name }}
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-ghost btn-sm"><x-icon name="logout" class="size-3.5"/> {{ __('Выйти') }}</button></form>
        </div>
        {{ $slot }}
    </main>
</div>
@include('partials.toast')
</body>
</html>
