@props(['kicker' => null, 'title', 'href' => null, 'link' => 'Смотреть все'])
<div class="mb-8 flex items-end justify-between gap-4">
    <div>
        @if($kicker)<p class="kicker mb-2"><span class="h-px w-6 bg-ember-500"></span>{{ $kicker }}</p>@endif
        <h2 class="section-title">{{ $title }}</h2>
    </div>
    @if($href)
        <a href="{{ $href }}" class="group hidden shrink-0 items-center gap-1.5 text-sm font-semibold text-ink-300 hover:text-white sm:inline-flex">
            {{ $link }} <x-icon name="arrow-right" class="size-4 transition group-hover:translate-x-0.5"/>
        </a>
    @endif
</div>
