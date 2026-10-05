@if ($paginator->hasPages())
<nav role="navigation" aria-label="{{ __('Пагинация') }}" class="mt-10 flex items-center justify-center gap-1.5">
    @if ($paginator->onFirstPage())
        <span class="grid size-10 place-items-center rounded-full text-ink-600"><x-icon name="arrow-left" class="size-4"/></span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="grid size-10 place-items-center rounded-full ring-1 ring-white/10 hover:bg-white/5" aria-label="{{ __('Назад') }}"><x-icon name="arrow-left" class="size-4"/></a>
    @endif
    @if(isset($elements))
        @foreach ($elements as $element)
            @if (is_string($element))<span class="px-2 text-ink-400">…</span>@endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="grid size-10 place-items-center rounded-full bg-ember-500 text-sm font-bold text-ink-950">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="grid size-10 place-items-center rounded-full text-sm font-semibold text-ink-300 hover:bg-white/5">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach
    @endif
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="grid size-10 place-items-center rounded-full ring-1 ring-white/10 hover:bg-white/5" aria-label="{{ __('Вперёд') }}"><x-icon name="arrow-right" class="size-4"/></a>
    @else
        <span class="grid size-10 place-items-center rounded-full text-ink-600"><x-icon name="arrow-right" class="size-4"/></span>
    @endif
</nav>
@endif
