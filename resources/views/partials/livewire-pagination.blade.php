@php($pageName = $paginator->getPageName())
@if ($paginator->hasPages())
<nav role="navigation" aria-label="Пагинация" class="mt-10 flex items-center justify-center gap-1.5">
    @if ($paginator->onFirstPage())
        <span class="grid size-10 place-items-center rounded-full text-ink-600"><x-icon name="arrow-left" class="size-4"/></span>
    @else
        <button type="button" wire:click="previousPage('{{ $pageName }}')" wire:loading.attr="disabled" class="grid size-10 place-items-center rounded-full ring-1 ring-white/10 hover:bg-white/5" aria-label="Назад"><x-icon name="arrow-left" class="size-4"/></button>
    @endif

    @if(isset($elements))
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-2 text-ink-400">…</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="grid size-10 place-items-center rounded-full bg-ember-500 text-sm font-bold text-ink-950">{{ $page }}</span>
                    @else
                        <button type="button" wire:click="gotoPage({{ $page }}, '{{ $pageName }}')" class="grid size-10 place-items-center rounded-full text-sm font-semibold text-ink-300 hover:bg-white/5">{{ $page }}</button>
                    @endif
                @endforeach
            @endif
        @endforeach
    @endif

    @if ($paginator->hasMorePages())
        <button type="button" wire:click="nextPage('{{ $pageName }}')" wire:loading.attr="disabled" class="grid size-10 place-items-center rounded-full ring-1 ring-white/10 hover:bg-white/5" aria-label="Вперёд"><x-icon name="arrow-right" class="size-4"/></button>
    @else
        <span class="grid size-10 place-items-center rounded-full text-ink-600"><x-icon name="arrow-right" class="size-4"/></span>
    @endif
</nav>
@endif
