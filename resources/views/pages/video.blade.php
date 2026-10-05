<x-layouts.app :title="$video->display_title" :description="\Illuminate\Support\Str::limit(strip_tags((string) $video->description), 160)" :og-image="$video->thumb_url">
@php
    $isShort = $video->type === 'short';
    $embed = 'https://www.youtube-nocookie.com/embed/'.$video->youtube_id.'?'.http_build_query([
        'enablejsapi' => 1, 'rel' => 0, 'modestbranding' => 1, 'playsinline' => 1, 'origin' => url('/'),
    ]);
    $shareUrl = urlencode(route('videos.show', $video->slug));
    $shareText = urlencode($video->title.' — Yst Qez');
    $chapters = $video->chapters();
@endphp

@if($isShort)
<div class="container-yq pt-6" data-feed data-prev-url="{{ $prev ? route('videos.show', $prev->slug) : '' }}" data-next-url="{{ $next ? route('videos.show', $next->slug) : '' }}">
    @if($next)<link rel="prefetch" href="{{ route('videos.show', $next->slug) }}">@endif
    <nav class="mb-5 flex items-center justify-between gap-2 text-sm text-ink-400" aria-label="Хлебные крошки">
        <a href="{{ route('videos.index') }}" class="inline-flex items-center gap-1.5 hover:text-white"><x-icon name="arrow-left" class="size-4"/> Все выпуски</a>
        <span class="hidden items-center gap-2 text-xs lg:inline-flex"><kbd class="rounded-md bg-white/10 px-1.5 py-0.5 font-mono">↑</kbd><kbd class="rounded-md bg-white/10 px-1.5 py-0.5 font-mono">↓</kbd> листать выпуски</span>
    </nav>

    <div class="grid gap-8 lg:grid-cols-[auto_minmax(0,1fr)] xl:grid-cols-[auto_minmax(0,1fr)_300px]">
        {{-- Vertical player + feed navigation --}}
        <div class="lg:sticky lg:top-20 lg:self-start">
            <div class="flex items-center justify-center gap-4">
                <div class="relative w-full max-w-[420px] lg:w-auto lg:max-w-none">
                    <div class="absolute -inset-6 -z-10 rounded-[3rem] bg-gradient-to-br from-ember-500/25 via-transparent to-sun-500/15 blur-3xl"></div>
                    <div class="aspect-[9/16] w-full overflow-hidden rounded-[1.75rem] bg-black shadow-2xl shadow-black/60 ring-1 ring-white/10 lg:h-[min(80vh,780px)] lg:w-auto">
                        <iframe id="yt-player" src="{{ $embed }}" title="{{ $video->display_title }}" class="h-full w-full" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="hidden flex-col gap-3 lg:flex">
                    @if($prev)
                        <a href="{{ route('videos.show', $prev->slug) }}" data-feed-prev class="grid size-12 place-items-center rounded-full bg-white/5 ring-1 ring-white/10 transition hover:bg-white/15" title="Предыдущий (↑)"><x-icon name="arrow-left" class="size-5 rotate-90"/></a>
                    @else
                        <span class="grid size-12 place-items-center rounded-full text-ink-600 ring-1 ring-white/5"><x-icon name="arrow-left" class="size-5 rotate-90"/></span>
                    @endif
                    @if($next)
                        <a href="{{ route('videos.show', $next->slug) }}" data-feed-next class="grid size-12 place-items-center rounded-full bg-gradient-to-br from-ember-500 to-sun-500 text-ink-950 shadow-lg shadow-ember-500/30 transition hover:scale-105" title="Следующий (↓)"><x-icon name="arrow-left" class="size-5 -rotate-90"/></a>
                    @else
                        <span class="grid size-12 place-items-center rounded-full text-ink-600 ring-1 ring-white/5"><x-icon name="arrow-left" class="size-5 -rotate-90"/></span>
                    @endif
                </div>
            </div>
            {{-- Mobile feed nav --}}
            <div class="mx-auto mt-4 grid max-w-[420px] grid-cols-2 gap-3 lg:hidden">
                @if($prev)<a href="{{ route('videos.show', $prev->slug) }}" class="btn btn-ghost"><x-icon name="arrow-left" class="size-4 rotate-90"/> Предыдущий</a>@else<span></span>@endif
                @if($next)<a href="{{ route('videos.show', $next->slug) }}" class="btn btn-primary">Следующий <x-icon name="arrow-left" class="size-4 -rotate-90"/></a>@endif
            </div>
        </div>

        {{-- Details --}}
        <article class="min-w-0">
            @include('partials.video.meta')
            @include('partials.video.actions')
            @include('partials.video.description')
            <livewire:video-comments :video-id="$video->id" />

            <div class="mt-12 xl:hidden">
                <h2 class="mb-4 font-sans text-sm font-bold uppercase tracking-wider text-ink-400">Ещё выпуски</h2>
                <div class="grid grid-cols-3 gap-3 sm:grid-cols-4">
                    @foreach($related->take(8) as $r)
                        <x-video-card :video="$r" />
                    @endforeach
                </div>
            </div>
        </article>

        {{-- Related shorts (desktop) --}}
        <aside class="hidden xl:block">
            <h2 class="mb-4 font-sans text-sm font-bold uppercase tracking-wider text-ink-400">Ещё выпуски</h2>
            <div class="grid grid-cols-2 gap-3">
                @forelse($related as $r)
                    <x-video-card :video="$r" />
                @empty
                    <p class="col-span-2 text-sm text-ink-400">Пока это единственный выпуск.</p>
                @endforelse
            </div>
            <a href="{{ route('collab') }}" class="card card-hover mt-6 block overflow-hidden p-5">
                <p class="kicker">Сотрудничество</p>
                <p class="mt-2 font-display text-base font-semibold">Ваш бренд в нашем разговоре</p>
                <p class="mt-1 text-sm text-ink-400">Интеграции, реклама, гости →</p>
            </a>
        </aside>
    </div>
</div>
@else
<div class="container-yq pt-6">
    <nav class="mb-5 flex items-center gap-2 text-sm text-ink-400" aria-label="Хлебные крошки">
        <a href="{{ route('videos.index') }}" class="inline-flex items-center gap-1.5 hover:text-white"><x-icon name="arrow-left" class="size-4"/> Все выпуски</a>
        <span>/</span><span class="text-ink-300">{{ $video->type_label }}</span>
    </nav>

    <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_380px]">
        <article class="min-w-0">
            {{-- Player --}}
            <div class="relative {{ $isShort ? 'mx-auto max-w-sm' : '' }}">
                <div class="absolute -inset-4 -z-10 rounded-[2.5rem] bg-gradient-to-br from-ember-500/20 via-transparent to-sun-500/10 blur-2xl"></div>
                <div class="{{ $isShort ? 'aspect-[9/16]' : 'aspect-video' }} overflow-hidden rounded-3xl bg-black shadow-2xl ring-1 ring-white/10">
                    <iframe id="yt-player" src="{{ $embed }}" title="{{ $video->display_title }}" class="h-full w-full" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </div>
            </div>

            <div class="mt-7">@include('partials.video.meta')</div>
            @include('partials.video.actions')
            @include('partials.video.description')
            @if($prev || $next)
            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                @if($prev)
                    <a href="{{ route('videos.show', $prev->slug) }}" class="card card-hover p-4">
                        <p class="text-xs font-semibold text-ink-400">← Более новый</p>
                        <p class="mt-1 line-clamp-1 font-bold">{{ $prev->title }}</p>
                    </a>
                @else <div></div> @endif
                @if($next)
                    <a href="{{ route('videos.show', $next->slug) }}" class="card card-hover p-4 text-right">
                        <p class="text-xs font-semibold text-ink-400">Следующий →</p>
                        <p class="mt-1 line-clamp-1 font-bold">{{ $next->title }}</p>
                    </a>
                @endif
            </div>
            @endif

            <livewire:video-comments :video-id="$video->id" />
        </article>

        <aside class="space-y-6">
            @include('partials.video.chapters')
            <div>
                <h2 class="mb-3 px-1.5 font-sans text-sm font-bold uppercase tracking-wider text-ink-400">Смотрите также</h2>
                <div class="space-y-1">
                    @forelse($related as $r)
                        <x-video-card :video="$r" compact />
                    @empty
                        <p class="px-1.5 text-sm text-ink-400">Пока это единственный выпуск в разделе.</p>
                    @endforelse
                </div>
            </div>

            <a href="{{ route('collab') }}" class="card card-hover block overflow-hidden p-5">
                <p class="kicker">Сотрудничество</p>
                <p class="mt-2 font-display text-lg font-semibold">Ваш бренд в нашем разговоре</p>
                <p class="mt-1 text-sm text-ink-400">Интеграции, реклама, гостевые выпуски →</p>
            </a>
        </aside>
    </div>
</div>
@endif
</x-layouts.app>
