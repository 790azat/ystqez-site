<div>
    @include('partials.admin-flash')
    <h1 class="text-3xl font-semibold">{{ __('Дашборд') }}</h1>
    <div class="mt-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach($stats as [$label, $value, $href])
            <a href="{{ $href }}" class="card card-hover p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-ink-400">{{ $label }}</p>
                <p class="mt-2 font-display text-3xl font-semibold">{{ number_format($value, 0, ',', ' ') }}</p>
            </a>
        @endforeach
    </div>
    <div class="mt-8 grid gap-6 xl:grid-cols-2">
        <div class="card p-6">
            <div class="flex items-center justify-between"><h2 class="font-display font-semibold">{{ __('Последние заявки') }}</h2><a href="{{ route('admin.collabs') }}" class="text-sm text-ember-300">{{ __('Все →') }}</a></div>
            <div class="mt-4 divide-y divide-white/5">
                @forelse($collabs as $c)
                    <div class="flex items-center justify-between gap-3 py-3 text-sm">
                        <div class="min-w-0"><p class="truncate font-bold">{{ $c->name }} <span class="font-normal text-ink-400">· {{ $c->type_label }}</span></p><p class="truncate text-xs text-ink-400">{{ $c->contact }} · {{ $c->created_at->diffForHumans() }}</p></div>
                        <span @class(['badge shrink-0', 'bg-ember-500/15 text-ember-300' => $c->status === 'new', 'bg-sun-500/15 text-sun-400' => $c->status === 'in_progress', 'bg-emerald-500/15 text-emerald-300' => $c->status === 'done'])>{{ $c->status_label }}</span>
                    </div>
                @empty
                    <p class="py-6 text-center text-sm text-ink-400">{{ __('Заявок пока нет') }}</p>
                @endforelse
            </div>
        </div>
        <div class="card p-6">
            <div class="flex items-center justify-between"><h2 class="font-display font-semibold">{{ __('Новые комментарии') }}</h2><a href="{{ route('admin.comments') }}" class="text-sm text-ember-300">{{ __('Все →') }}</a></div>
            <div class="mt-4 divide-y divide-white/5">
                @forelse($comments as $c)
                    <div class="py-3 text-sm">
                        <p><b>{{ $c->author_name }}</b> <span class="text-ink-400">→ {{ \Illuminate\Support\Str::limit($c->video?->title, 50) }}</span></p>
                        <p class="mt-0.5 line-clamp-2 text-ink-300">{{ $c->body }}</p>
                    </div>
                @empty
                    <p class="py-6 text-center text-sm text-ink-400">{{ __('Комментариев пока нет') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
