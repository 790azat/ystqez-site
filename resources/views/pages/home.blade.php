<x-layouts.app>
@php
    $banner = media_url(setting('channel_banner'));
    $avatar = media_url(setting('channel_avatar'));
    $subs = setting('channel_subscribers');
    $igFollowers = setting('instagram_followers');
@endphp

{{-- ============ HERO ============ --}}
<section class="relative -mt-16 overflow-hidden pt-16">
    <div class="absolute inset-0 -z-10">
        @if($banner)
            <img src="{{ $banner }}" alt="" class="h-full w-full object-cover opacity-25 blur-[2px]" referrerpolicy="no-referrer" data-hide-on-error>
        @endif
        <div class="absolute inset-0 bg-gradient-to-b from-ink-950/40 via-ink-950/80 to-ink-950"></div>
        <div class="absolute -right-40 top-10 size-[38rem] rounded-full bg-ember-500/20 blur-[120px]"></div>
        <div class="absolute -left-40 bottom-0 size-[30rem] rounded-full bg-sun-500/10 blur-[120px]"></div>
    </div>

    <div class="container-yq grid items-center gap-12 py-16 lg:grid-cols-[1.05fr_1fr] lg:py-24">
        <div class="animate-fade-up">
            <div class="flex items-center gap-3">
                @if($avatar)
                    <img src="{{ $avatar }}" alt="" class="size-12 rounded-full object-cover ring-2 ring-ember-500/60" referrerpolicy="no-referrer">
                @endif
                <span class="kicker"><span class="size-2 rounded-full bg-ember-500 animate-pulse-dot"></span> YouTube · Instagram · {{ config('site.youtube_handle') }}</span>
            </div>
            <h1 class="mt-6 text-4xl font-semibold leading-[1.08] sm:text-5xl lg:text-6xl">
                <span class="gradient-text">{{ setting('hero_title') }}</span>
            </h1>
            <p class="mt-6 max-w-xl text-base leading-relaxed text-ink-300 sm:text-lg">{{ setting('hero_subtitle') }}</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('videos.index') }}" class="btn btn-primary btn-lg"><x-icon name="play-fill" class="size-5"/> Смотреть выпуски</a>
                <a href="{{ setting('youtube_url') }}" target="_blank" rel="noopener" class="btn btn-ghost btn-lg"><x-icon name="youtube" class="size-5 text-ember-400"/> Подписаться</a>
            </div>
            @php
                $heroStats = array_values(array_filter([
                    ['Выпусков', $stats['videos'] ? number_format($stats['videos'], 0, ',', ' ') : null],
                    ['Подписчиков YouTube', $subs ? \App\Support\Ru::compact((int) $subs) : null],
                    ['Подписчиков Instagram', $igFollowers ? \App\Support\Ru::compact((int) $igFollowers) : null],
                    ['Просмотров', !$subs && $stats['views'] ? \App\Support\Ru::compact($stats['views']) : null],
                    ['Лет дружбы', '7+'],
                ], fn ($s) => $s[1] !== null));
            @endphp
            <dl class="mt-10 grid max-w-xl grid-cols-2 gap-x-6 gap-y-5 border-t border-white/10 pt-6 {{ [1 => 'sm:grid-cols-1', 2 => 'sm:grid-cols-2', 3 => 'sm:grid-cols-3', 4 => 'sm:grid-cols-4'][min(count($heroStats), 4)] }}">
                @foreach(array_slice($heroStats, 0, 4) as [$label, $value])
                    <div>
                        <dt class="text-xs text-ink-400">{{ $label }}</dt>
                        <dd class="mt-1 font-display text-2xl font-semibold">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        <div class="animate-fade-up [animation-delay:.15s]">
            @if($showcase->isNotEmpty())
                <div class="relative mx-auto flex h-[30rem] max-w-md items-center justify-center sm:h-[34rem]">
                    <div class="absolute inset-10 rounded-full bg-gradient-to-br from-ember-500/30 to-sun-500/10 blur-3xl"></div>
                    @foreach($showcase as $i => $s)
                        @php
                            $pos = [
                                0 => 'z-20 w-56 sm:w-64',
                                1 => 'absolute left-0 z-10 w-40 -rotate-[8deg] opacity-80 sm:w-48 hover:opacity-100',
                                2 => 'absolute right-0 z-10 w-40 rotate-[8deg] opacity-80 sm:w-48 hover:opacity-100',
                            ][$i];
                        @endphp
                        <a href="{{ route('videos.show', $s->slug) }}" class="group {{ $pos }} transition duration-500 hover:z-30 hover:scale-[1.03]">
                            <div class="thumb aspect-[9/16] rounded-[1.75rem] shadow-2xl shadow-black/60 ring-1 ring-white/15">
                                <img src="{{ $s->thumb_url }}" data-fallback="{{ $s->fallback_thumb }}" alt="" referrerpolicy="no-referrer">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/10 to-transparent"></div>
                                @if($i === 0)
                                    <span class="absolute left-1/2 top-1/2 grid size-16 -translate-x-1/2 -translate-y-1/2 place-items-center rounded-full bg-white/95 text-ink-950 shadow-2xl transition group-hover:scale-110">
                                        <x-icon name="play-fill" class="size-7 translate-x-0.5"/>
                                    </span>
                                    <div class="absolute inset-x-0 bottom-0 p-4">
                                        <span class="badge bg-ember-500 text-ink-950">Новое</span>
                                        <p class="mt-2 line-clamp-3 text-sm font-bold leading-snug">{{ $s->display_title }}</p>
                                    </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @elseif($featured)
                <a href="{{ route('videos.show', $featured->slug) }}" class="group relative block">
                    <div class="absolute -inset-3 rounded-[2rem] bg-gradient-to-br from-ember-500/30 to-sun-500/10 opacity-60 blur-2xl transition group-hover:opacity-90"></div>
                    <div class="thumb relative aspect-video rounded-[1.75rem] ring-1 ring-white/10">
                        <img src="https://i.ytimg.com/vi/{{ $featured->youtube_id }}/maxresdefault.jpg" data-fallback="{{ $featured->thumb_url }}" alt="{{ $featured->display_title }}" referrerpolicy="no-referrer">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"></div>
                        <span class="absolute left-1/2 top-1/2 grid size-20 -translate-x-1/2 -translate-y-1/2 place-items-center rounded-full bg-white text-ink-950 shadow-2xl transition duration-300 group-hover:scale-110">
                            <x-icon name="play-fill" class="size-8 translate-x-0.5"/>
                        </span>
                        <div class="absolute inset-x-0 bottom-0 p-5 sm:p-7">
                            <span class="badge bg-ember-500 text-ink-950">Новый выпуск</span>
                            <h2 class="mt-3 line-clamp-2 font-sans text-lg font-bold leading-snug sm:text-2xl">{{ $featured->display_title }}</h2>
                            <p class="mt-2 text-xs text-ink-300">{{ $featured->published_at?->translatedFormat('j F Y') }}@if($featured->duration_human) · {{ $featured->duration_human }}@endif</p>
                        </div>
                    </div>
                </a>
            @else
                <div class="card noise relative overflow-hidden p-8 sm:p-10">
                    <div class="absolute -right-10 -top-10 size-48 rounded-full bg-ember-500/20 blur-3xl"></div>
                    <x-icon name="mic" class="size-10 text-ember-400"/>
                    <h2 class="mt-6 text-2xl font-semibold">Новые разговоры — уже в пути</h2>
                    <p class="mt-3 text-ink-300">Выпуски появятся здесь, как только мы синхронизируем канал. А пока — загляните на YouTube.</p>
                    <a href="{{ setting('youtube_url') }}" target="_blank" rel="noopener" class="btn btn-primary mt-6"><x-icon name="youtube" class="size-5"/> Открыть канал</a>
                </div>
            @endif
        </div>
    </div>
</section>

{{-- ============ LATEST ============ --}}
@if($shorts->isNotEmpty())
<section class="container-yq mt-8">
    <x-section-head kicker="Свежее" title="Новые выпуски" :href="route('videos.index')" />
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 md:grid-cols-4 xl:grid-cols-6">
        @foreach($shorts as $video)
            <x-video-card :video="$video" />
        @endforeach
    </div>
</section>
@endif

@if($longs->isNotEmpty())
<section class="container-yq {{ $shorts->isNotEmpty() ? 'mt-24' : 'mt-8' }}">
    <x-section-head kicker="Длинные разговоры" title="Выпуски" :href="route('videos.index', ['type' => 'video'])" />
    <div class="grid gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($longs as $video)
            <x-video-card :video="$video" />
        @endforeach
    </div>
</section>
@endif

@if($shorts->isEmpty() && $longs->isEmpty() && !$featured)
<section class="container-yq mt-8">
    <x-empty icon="film" title="Здесь скоро будут выпуски" text="Мы готовим каталог разговоров. Подпишитесь на канал, чтобы не пропустить новые.">
        <a href="{{ setting('youtube_url') }}" target="_blank" rel="noopener" class="btn btn-ghost"><x-icon name="youtube" class="size-4"/> YouTube</a>
    </x-empty>
</section>
@endif

{{-- ============ ABOUT + CHAT ============ --}}
<section class="container-yq mt-24 grid gap-6 lg:grid-cols-[1.2fr_1fr]">
    <div class="card noise relative overflow-hidden p-8 sm:p-10">
        <div class="absolute -left-16 -top-16 size-56 rounded-full bg-sun-500/10 blur-3xl"></div>
        <p class="kicker"><span class="h-px w-6 bg-ember-500"></span>Кто мы</p>
        <h2 class="mt-3 text-3xl font-semibold">Друзья, дорога и <span class="gradient-text">долгие разговоры</span></h2>
        <p class="mt-5 leading-relaxed text-ink-300">{{ setting('about_text') }}</p>
        <div class="mt-8 grid gap-4 sm:grid-cols-3">
            @foreach([['compass', 'В дороге', 'Записываем разговоры там, куда приводят путешествия'], ['mic', 'Без сценария', 'Живой диалог вместо интервью по бумажке'], ['book', 'Про смыслы', 'Идеи, книги, люди и то, что между строк']] as [$icon, $h, $t])
                <div class="rounded-2xl bg-white/[.03] p-4 ring-1 ring-white/5">
                    <x-icon :name="$icon" class="size-5 text-ember-400"/>
                    <h3 class="mt-3 font-sans text-sm font-bold">{{ $h }}</h3>
                    <p class="mt-1 text-xs leading-relaxed text-ink-400">{{ $t }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="card flex flex-col p-6 sm:p-8">
        <div class="flex items-center justify-between">
            <div>
                <p class="kicker"><span class="size-2 rounded-full bg-emerald-400 animate-pulse-dot"></span>Live</p>
                <h2 class="mt-2 text-2xl font-semibold">Живой чат</h2>
            </div>
            <x-icon name="chat" class="size-8 text-ink-600"/>
        </div>
        <div class="mt-6 flex-1 space-y-3">
            @forelse($chat as $m)
                <div class="flex gap-3">
                    <span class="avatar size-8 text-xs">{{ mb_strtoupper(mb_substr($m->nickname, 0, 1)) }}</span>
                    <div class="min-w-0 rounded-2xl rounded-tl-sm bg-white/[.04] px-3.5 py-2">
                        <p class="text-xs font-bold text-ember-300">{{ $m->nickname }}</p>
                        <p class="line-clamp-2 break-words text-sm text-ink-200">{{ $m->body }}</p>
                    </div>
                </div>
            @empty
                <p class="rounded-2xl bg-white/[.03] p-5 text-sm text-ink-400">В чате пока тихо. Будьте первым, кто скажет «привет» 👋</p>
            @endforelse
        </div>
        <a href="{{ route('chat') }}" class="btn btn-primary mt-6 w-full"><x-icon name="chat" class="size-4"/> Войти в чат</a>
    </div>
</section>

{{-- ============ FORUM ============ --}}
<section class="container-yq mt-24">
    <x-section-head kicker="Сообщество" title="Обсуждают на форуме" :href="route('forum.index')" link="Весь форум" />
    @if($topics->isNotEmpty())
        <div class="card divide-y divide-white/5">
            @foreach($topics as $topic)
                <a href="{{ route('forum.topic', $topic) }}" class="group flex items-center gap-4 p-5 transition hover:bg-white/[.02]">
                    <span class="grid size-11 shrink-0 place-items-center rounded-2xl bg-white/5 text-xl">{{ $topic->category?->emoji ?? '💬' }}</span>
                    <div class="min-w-0 flex-1">
                        <h3 class="truncate font-sans font-bold group-hover:text-white">
                            @if($topic->is_pinned)<x-icon name="pin" class="mr-1 inline size-4 text-sun-400"/>@endif{{ $topic->title }}
                        </h3>
                        <p class="mt-0.5 text-xs text-ink-400">{{ $topic->category?->name }} · {{ $topic->user?->name ?? 'Гость' }} · {{ $topic->last_post_at?->diffForHumans() }}</p>
                    </div>
                    <span class="hidden shrink-0 items-center gap-1.5 rounded-full bg-white/5 px-3 py-1 text-xs font-semibold text-ink-300 sm:inline-flex">
                        <x-icon name="reply" class="size-3.5"/> {{ $topic->replies_count }}
                    </span>
                </a>
            @endforeach
        </div>
    @else
        <x-empty icon="users" title="Форум ждёт первую тему" text="Предложите идею выпуска, поделитесь книгой или расскажите о путешествии.">
            <a href="{{ route('forum.index') }}" class="btn btn-primary">Открыть форум</a>
        </x-empty>
    @endif
</section>

{{-- ============ INSTAGRAM ============ --}}
<section class="container-yq mt-24">
    <x-section-head kicker="{{ '@'.config('site.instagram_handle') }}" title="Из Instagram" :href="route('instagram')" link="Все публикации" />
    @if($instagram->isNotEmpty())
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            @foreach($instagram as $post)
                @include('partials.insta-tile', ['post' => $post])
            @endforeach
        </div>
    @else
        <x-empty icon="instagram" title="Кадры из поездок" text="Фото и закулисье — в нашем Instagram.">
            <a href="{{ setting('instagram_url') }}" target="_blank" rel="noopener" class="btn btn-ghost"><x-icon name="instagram" class="size-4"/> Открыть Instagram</a>
        </x-empty>
    @endif
</section>

{{-- ============ COLLAB CTA ============ --}}
<section class="container-yq mt-24">
    <div class="noise relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-ember-600 via-ember-500 to-sun-500 p-8 text-ink-950 sm:p-14">
        <div class="absolute -right-24 -top-24 size-80 rounded-full bg-white/20 blur-3xl"></div>
        <div class="relative grid items-center gap-8 lg:grid-cols-[1.5fr_1fr]">
            <div>
                <p class="text-xs font-bold uppercase tracking-[.2em] opacity-70">Сотрудничество</p>
                <h2 class="mt-3 text-3xl font-semibold leading-tight sm:text-4xl">Хотите сделать что-то вместе?</h2>
                <p class="mt-4 max-w-xl font-medium opacity-80">Реклама, нативная интеграция, приглашение в гости или совместный проект — расскажите о своей идее, и мы ответим.</p>
            </div>
            <div class="flex flex-wrap gap-3 lg:justify-end">
                <a href="{{ route('collab') }}" class="btn btn-lg bg-ink-950 text-white hover:bg-ink-800">Оставить заявку <x-icon name="arrow-right" class="size-5"/></a>
                <a href="{{ route('collab', ['type' => 'guest']) }}" class="btn btn-lg bg-white/25 text-ink-950 hover:bg-white/40">Стать гостем</a>
            </div>
        </div>
    </div>
</section>
</x-layouts.app>
