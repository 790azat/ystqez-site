@props(['video', 'compact' => false])
@if($compact)
<a href="{{ route('videos.show', $video->slug) }}" class="group flex gap-3 rounded-2xl p-1.5 transition hover:bg-white/[.04]">
    <div class="thumb aspect-video w-40 shrink-0 rounded-xl">
        <img src="{{ $video->thumb_url }}" data-fallback="{{ $video->fallback_thumb }}" alt="" loading="lazy" referrerpolicy="no-referrer">
        @if($video->duration_human)
            <span class="absolute bottom-1.5 right-1.5 rounded-md bg-black/80 px-1.5 py-0.5 text-[11px] font-bold">{{ $video->duration_human }}</span>
        @endif
    </div>
    <div class="min-w-0 py-0.5">
        <h3 class="line-clamp-2 font-sans text-sm font-bold leading-snug text-ink-100 group-hover:text-white">{{ $video->display_title }}</h3>
        <p class="mt-1.5 text-xs text-ink-400">{{ $video->published_at?->translatedFormat('j M Y') }}@if($video->view_count) · {{ \App\Support\Ru::compact($video->view_count) }}@endif</p>
    </div>
</a>
@elseif($video->type === 'short')
<a href="{{ route('videos.show', $video->slug) }}" class="group block">
    <div class="thumb aspect-[9/16] rounded-2xl ring-1 ring-white/5 transition group-hover:ring-ember-500/40">
        <img src="{{ $video->thumb_url }}" data-fallback="{{ $video->fallback_thumb }}" alt="" loading="lazy" referrerpolicy="no-referrer">
        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/10 to-transparent"></div>
        @if($video->view_count)<span class="absolute left-2.5 top-2.5 inline-flex items-center gap-1 rounded-full bg-black/55 px-2 py-0.5 text-[11px] font-bold backdrop-blur"><x-icon name="eye" class="size-3"/> {{ \App\Support\Ru::compact($video->view_count) }}</span>@endif
        <span class="absolute left-1/2 top-1/2 grid size-12 -translate-x-1/2 -translate-y-1/2 scale-75 place-items-center rounded-full bg-white/90 text-ink-950 opacity-0 shadow-2xl transition duration-300 group-hover:scale-100 group-hover:opacity-100"><x-icon name="play-fill" class="size-5 translate-x-0.5"/></span>
        <div class="absolute inset-x-0 bottom-0 p-3 sm:p-4">
            <h3 class="line-clamp-3 font-sans text-[13px] font-bold leading-snug sm:text-sm">{{ $video->display_title }}</h3>
            @if($video->published_at)<p class="mt-1 text-[11px] text-ink-300">{{ $video->published_at->translatedFormat('j M Y') }}</p>@endif
        </div>
    </div>
</a>
@else
<a href="{{ route('videos.show', $video->slug) }}" {{ $attributes->merge(['class' => 'group block']) }}>
    <div class="thumb aspect-video">
        <img src="{{ $video->thumb_url }}" data-fallback="{{ $video->fallback_thumb }}" alt="" loading="lazy" referrerpolicy="no-referrer">
        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 transition group-hover:opacity-100"></div>
        <span class="absolute left-1/2 top-1/2 grid size-14 -translate-x-1/2 -translate-y-1/2 scale-75 place-items-center rounded-full bg-white/90 text-ink-950 opacity-0 shadow-2xl transition duration-300 group-hover:scale-100 group-hover:opacity-100">
            <x-icon name="play-fill" class="size-6 translate-x-0.5"/>
        </span>
        @if($video->type === 'live')
            <span class="badge absolute left-3 top-3 bg-red-500 text-white"><span class="size-1.5 rounded-full bg-white animate-pulse-dot"></span> Эфир</span>
        @endif
        @if($video->is_featured)
            <span class="badge absolute right-3 top-3 bg-sun-500 text-ink-950"><x-icon name="star" class="size-3"/> Выбор</span>
        @endif
        @if($video->duration_human)
            <span class="absolute bottom-2.5 right-2.5 rounded-md bg-black/80 px-1.5 py-0.5 text-xs font-bold backdrop-blur">{{ $video->duration_human }}</span>
        @endif
    </div>
    <div class="mt-3.5 px-1">
        <h3 class="line-clamp-2 font-sans text-[15px] font-bold leading-snug text-ink-100 transition group-hover:text-white">{{ $video->display_title }}</h3>
        <p class="mt-1.5 flex items-center gap-2 text-xs text-ink-400">
            <span>{{ $video->published_at?->translatedFormat('j F Y') }}</span>
            @if($video->view_count)<span class="size-1 rounded-full bg-ink-600"></span><span>{{ $video->views_human }}</span>@endif
        </p>
    </div>
</a>
@endif
