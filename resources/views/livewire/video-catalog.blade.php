<div class="container-yq pt-10">
    <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="kicker"><span class="h-px w-6 bg-ember-500"></span>Каталог</p>
            <h1 class="mt-2 text-4xl font-semibold sm:text-5xl">Выпуски</h1>
            <p class="mt-3 max-w-xl text-ink-400">Все разговоры Yst Qez — короткие мысли, споры и истории из дороги. {{ $counts->sum() }} {{ \App\Support\Ru::plural((int) $counts->sum(), ['выпуск', 'выпуска', 'выпусков']) }} и счёт продолжается.</p>
        </div>
        <div class="relative w-full lg:w-96">
            <x-icon name="search" class="pointer-events-none absolute left-4 top-1/2 size-4 -translate-y-1/2 text-ink-400"/>
            <input type="search" wire:model.live.debounce.350ms="search" placeholder="Поиск по названию и описанию…" class="input pl-11" aria-label="Поиск">
        </div>
    </div>

    <div class="mt-8 flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2">
            <button wire:click="setType('')" @class(['chip', 'chip-active' => $type === ''])>Все <span class="opacity-60">{{ $counts->sum() }}</span></button>
            @foreach(\App\Models\Video::TYPES as $key => $label)
                @continue(($counts[$key] ?? 0) === 0 && $type !== $key)
                <button wire:click="setType('{{ $key }}')" @class(['chip', 'chip-active' => $type === $key])>
                    <x-icon :name="['video' => 'film', 'short' => 'zap', 'live' => 'live'][$key]" class="size-3.5"/>
                    {{ $label }} <span class="opacity-60">{{ $counts[$key] ?? 0 }}</span>
                </button>
            @endforeach
            @if($tag !== '')
                <button wire:click="$set('tag', '')" class="chip chip-active">#{{ $tag }} <x-icon name="x" class="size-3"/></button>
            @endif
        </div>
        <div class="flex items-center gap-1 rounded-full bg-white/5 p-1 ring-1 ring-white/10">
            @foreach(['new' => 'Новые', 'popular' => 'Популярные', 'old' => 'Старые'] as $key => $label)
                <button wire:click="$set('sort', '{{ $key }}')" @class(['rounded-full px-3.5 py-1.5 text-xs font-bold transition', 'bg-white text-ink-950' => $sort === $key, 'text-ink-300 hover:text-white' => $sort !== $key])>{{ $label }}</button>
            @endforeach
        </div>
    </div>

    <div class="relative mt-10">
        <div wire:loading.delay class="absolute inset-0 z-10 rounded-3xl bg-ink-950/50 backdrop-blur-[2px]"></div>
        @if($videos->isNotEmpty())
            @php($allShorts = $videos->every(fn ($v) => $v->type === 'short'))
            <div @class([
                'grid',
                'grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 md:grid-cols-4 xl:grid-cols-6' => $allShorts,
                'gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3' => ! $allShorts,
            ])>
                @foreach($videos as $video)
                    <x-video-card :video="$video" wire:key="v{{ $video->id }}" />
                @endforeach
            </div>
            {{ $videos->links() }}
        @elseif($search !== '' || $type !== '' || $tag !== '')
            <x-empty icon="search" title="Ничего не нашлось" text="Попробуйте изменить запрос или сбросить фильтры.">
                <button wire:click="clearFilters" class="btn btn-ghost">Сбросить фильтры</button>
            </x-empty>
        @else
            <x-empty icon="film" title="Каталог пока пуст" text="Совсем скоро здесь появятся все выпуски канала. А пока их можно посмотреть на YouTube.">
                <a href="{{ setting('youtube_url') }}" target="_blank" rel="noopener" class="btn btn-primary"><x-icon name="youtube" class="size-4"/> Канал на YouTube</a>
            </x-empty>
        @endif
    </div>
</div>
