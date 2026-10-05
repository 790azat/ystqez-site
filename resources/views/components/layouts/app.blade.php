@props(['title' => null, 'description' => null, 'ogImage' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    @include('partials.head', ['title' => $title ?? null, 'description' => $description ?? null, 'ogImage' => $ogImage ?? null])
</head>
<body class="min-h-screen flex flex-col">
@php
    $nav = [
        ['home', __('Главная'), 'home'],
        ['videos.index', __('Выпуски'), 'videos.*'],
        ['forum.index', __('Форум'), 'forum.*'],
        ['chat', __('Чат'), 'chat'],
        ['instagram', 'Instagram', 'instagram'],
        ['collab', __('Сотрудничество'), 'collab'],
    ];
@endphp
<header x-data="{ open: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 8"
        :class="scrolled ? 'bg-ink-950/85 backdrop-blur-xl ring-1 ring-white/5' : 'bg-transparent'"
        class="sticky top-0 z-40 transition-colors duration-300">
    <div class="container-yq flex h-16 items-center justify-between gap-4">
        @include('partials.logo')

        <nav class="hidden items-center gap-1 lg:flex" aria-label="{{ __('Основная навигация') }}">
            @foreach($nav as [$route, $label, $pattern])
                <a href="{{ route($route) }}" @class(['nav-link', 'nav-link-active' => request()->routeIs($pattern)])>{{ $label }}</a>
            @endforeach
        </nav>

        <div class="flex items-center gap-2">
            @include('partials.lang-switch', ['class' => 'hidden md:inline-flex'])
            <a href="{{ setting('youtube_url') }}" target="_blank" rel="noopener" class="btn btn-ghost btn-sm hidden sm:inline-flex">
                <x-icon name="youtube" class="size-4 text-ember-400"/> {{ __('Подписаться') }}
            </a>
            @auth
                <div class="relative" x-data="{ menu: false }" @click.outside="menu = false">
                    <button @click="menu = !menu" class="flex items-center gap-2 rounded-full p-1 pr-3 ring-1 ring-white/10 hover:bg-white/5" aria-label="{{ __('Меню профиля') }}">
                        <span class="avatar size-7 text-xs">{{ auth()->user()->initials() }}</span>
                        <span class="hidden max-w-28 truncate text-sm font-semibold sm:block">{{ auth()->user()->name }}</span>
                    </button>
                    <div x-cloak x-show="menu" x-transition.origin.top.right
                         class="absolute right-0 mt-2 w-52 overflow-hidden rounded-2xl bg-ink-800 p-1.5 shadow-2xl ring-1 ring-white/10">
                        @if(auth()->user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm hover:bg-white/5"><x-icon name="shield" class="size-4"/> {{ __('Админка') }}</a>
                        @endif
                        <a href="{{ route('forum.index') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm hover:bg-white/5"><x-icon name="users" class="size-4"/> {{ __('Форум') }}</a>
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-left text-sm text-ink-300 hover:bg-white/5"><x-icon name="logout" class="size-4"/> {{ __('Выйти') }}</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">{{ __('Войти') }}</a>
            @endauth
            <button @click="open = !open" class="grid size-10 place-items-center rounded-full ring-1 ring-white/10 lg:hidden" aria-label="{{ __('Меню') }}">
                <x-icon name="menu" class="size-5" x-show="!open"/>
                <x-icon name="x" class="size-5" x-cloak x-show="open"/>
            </button>
        </div>
    </div>
    <nav x-cloak x-show="open" x-transition class="container-yq grid gap-1 pb-4 lg:hidden" aria-label="{{ __('Мобильная навигация') }}">
        @foreach($nav as [$route, $label, $pattern])
            <a href="{{ route($route) }}" @class(['nav-link py-3', 'nav-link-active' => request()->routeIs($pattern)])>{{ $label }}</a>
        @endforeach
        <div class="flex items-center justify-between gap-3 px-3.5 pt-3 md:hidden">
            <span class="text-xs font-semibold uppercase tracking-wider text-ink-400">{{ __('Язык') }}</span>
            @include('partials.lang-switch')
        </div>
    </nav>
</header>

<main class="flex-1">
    {{ $slot }}
</main>

<footer class="mt-24 border-t border-white/5 bg-ink-900/60">
    <div class="container-yq grid gap-10 py-14 md:grid-cols-[1.4fr_1fr_1fr]">
        <div class="max-w-sm">
            @include('partials.logo')
            <p class="mt-4 text-sm leading-relaxed text-ink-400">{{ __('Интеллектуальные разговоры друзей, которые много лет путешествуют вместе. Без сценария — только идеи, споры и смыслы.') }}</p>
            <div class="mt-5 flex gap-2">
                <a href="{{ setting('youtube_url') }}" target="_blank" rel="noopener" class="grid size-10 place-items-center rounded-full bg-white/5 ring-1 ring-white/10 hover:bg-white/10" aria-label="YouTube"><x-icon name="youtube" class="size-5"/></a>
                <a href="{{ setting('instagram_url') }}" target="_blank" rel="noopener" class="grid size-10 place-items-center rounded-full bg-white/5 ring-1 ring-white/10 hover:bg-white/10" aria-label="Instagram"><x-icon name="instagram" class="size-5"/></a>
                @if(setting('telegram_url'))
                    <a href="{{ setting('telegram_url') }}" target="_blank" rel="noopener" class="grid size-10 place-items-center rounded-full bg-white/5 ring-1 ring-white/10 hover:bg-white/10" aria-label="Telegram"><x-icon name="telegram" class="size-5"/></a>
                @endif
            </div>
        </div>
        <div>
            <h3 class="font-sans text-xs font-bold uppercase tracking-widest text-ink-400">{{ __('Разделы') }}</h3>
            <ul class="mt-4 grid gap-2 text-sm">
                @foreach($nav as [$route, $label])
                    <li><a href="{{ route($route) }}" class="text-ink-300 hover:text-white">{{ $label }}</a></li>
                @endforeach
            </ul>
        </div>
        <div>
            <h3 class="font-sans text-xs font-bold uppercase tracking-widest text-ink-400">{{ __('Связь') }}</h3>
            <ul class="mt-4 grid gap-2 text-sm text-ink-300">
                @if(setting('contact_email'))<li><a href="mailto:{{ setting('contact_email') }}" class="hover:text-white">{{ setting('contact_email') }}</a></li>@endif
                @if(setting('contact_telegram'))<li>Telegram: {{ setting('contact_telegram') }}</li>@endif
                <li><a href="{{ route('collab') }}" class="hover:text-white">{{ __('Предложить сотрудничество →') }}</a></li>
                <li><a href="{{ route('forum.index') }}" class="hover:text-white">{{ __('Предложить тему выпуска →') }}</a></li>
            </ul>
        </div>
    </div>
    <div class="container-yq flex flex-col gap-3 border-t border-white/5 py-6 text-xs text-ink-400 sm:flex-row sm:items-center sm:justify-between">
        <span>© {{ date('Y') }} Yst Qez. {{ __('Сделано с любовью к долгим разговорам.') }}</span>
        @include('partials.lang-switch', ['class' => 'self-start sm:self-auto'])
        <span>YouTube {{ config('site.youtube_handle') }} · Instagram {{ '@'.config('site.instagram_handle') }}</span>
    </div>
</footer>

@unless(request()->routeIs('chat'))
    <livewire:chat-room mode="widget" :raised="request()->routeIs('videos.show')" />
@endunless

@include('partials.toast')
</body>
</html>
