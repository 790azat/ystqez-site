@php($current = app()->getLocale())
<div class="inline-flex items-center gap-0.5 rounded-full bg-white/5 p-0.5 ring-1 ring-white/10 {{ $class ?? '' }}" role="group" aria-label="{{ __('Язык сайта') }}">
    @foreach(\App\Support\Locale::SUPPORTED as $loc => $short)
        <a href="{{ route('locale', $loc) }}" hreflang="{{ $loc }}" lang="{{ $loc }}" title="{{ \App\Support\Locale::NAMES[$loc] }}" rel="nofollow"
           @if($loc === $current) aria-current="true" @endif
           @class(['rounded-full px-2.5 py-1 text-[11px] font-bold tracking-wide transition', 'bg-ember-500 text-ink-950 shadow-sm' => $loc === $current, 'text-ink-300 hover:bg-white/10 hover:text-white' => $loc !== $current])>{{ $short }}</a>
    @endforeach
</div>
