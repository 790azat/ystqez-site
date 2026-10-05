@props(['icon' => 'sparkles', 'title', 'text' => null])
<div {{ $attributes->merge(['class' => 'empty-state']) }}>
    <span class="grid size-14 place-items-center rounded-2xl bg-gradient-to-br from-ember-500/20 to-sun-500/10 text-ember-300 ring-1 ring-ember-500/20">
        <x-icon :name="$icon" class="size-6"/>
    </span>
    <h3 class="mt-5 font-display text-lg font-semibold">{{ $title }}</h3>
    @if($text)<p class="mt-2 max-w-md text-sm leading-relaxed text-ink-400">{{ $text }}</p>@endif
    @if(trim($slot))<div class="mt-6 flex flex-wrap justify-center gap-3">{{ $slot }}</div>@endif
</div>
