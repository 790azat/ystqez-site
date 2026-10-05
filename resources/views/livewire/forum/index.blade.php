<div class="container-yq pt-10">
    <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="kicker"><span class="h-px w-6 bg-ember-500"></span>Сообщество</p>
            <h1 class="mt-2 text-4xl font-semibold sm:text-5xl">Форум</h1>
            <p class="mt-3 max-w-xl text-ink-400">Место для неспешных разговоров: идеи выпусков, книги, путешествия и всё, что не помещается в комментарии.</p>
        </div>
        <div class="flex gap-6 text-sm">
            @foreach([['Тем', $stats['topics']], ['Сообщений', $stats['posts']], ['Участников', $stats['users']]] as [$l, $n])
                <div><p class="font-display text-2xl font-semibold">{{ $n }}</p><p class="text-xs text-ink-400">{{ $l }}</p></div>
            @endforeach
        </div>
    </div>

    @guest
        <div class="card mt-8 flex flex-col items-start justify-between gap-4 p-5 sm:flex-row sm:items-center">
            <p class="text-sm text-ink-300"><x-icon name="users" class="mr-2 inline size-4 text-ember-400"/>Читать форум можно всем. Чтобы создавать темы и отвечать — войдите или зарегистрируйтесь.</p>
            <div class="flex gap-2">
                <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Войти</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Регистрация</a>
            </div>
        </div>
    @endguest

    <div class="mt-10 grid gap-8 lg:grid-cols-[minmax(0,1fr)_340px]">
        <div class="space-y-3">
            @forelse($categories as $cat)
                <a href="{{ route('forum.category', $cat) }}" wire:key="cat{{ $cat->id }}" class="card card-hover group flex items-center gap-5 p-5 sm:p-6">
                    <span class="grid size-14 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-white/10 to-white/[.02] text-2xl ring-1 ring-white/10">{{ $cat->emoji ?: '💬' }}</span>
                    <div class="min-w-0 flex-1">
                        <h2 class="font-display text-lg font-semibold group-hover:text-white">{{ $cat->name }}</h2>
                        @if($cat->description)<p class="mt-1 line-clamp-1 text-sm text-ink-400">{{ $cat->description }}</p>@endif
                        @if($cat->latest_topic)
                            <p class="mt-2 truncate text-xs text-ink-400">Последняя: <span class="text-ink-200">{{ $cat->latest_topic->title }}</span> · {{ $cat->latest_topic->last_post_at?->diffForHumans() }}</p>
                        @endif
                    </div>
                    <div class="hidden shrink-0 gap-6 text-center sm:flex">
                        <div><p class="font-display text-lg font-semibold">{{ $cat->topics_count }}</p><p class="text-[11px] text-ink-400">тем</p></div>
                        <div><p class="font-display text-lg font-semibold">{{ $cat->posts_count }}</p><p class="text-[11px] text-ink-400">сообщений</p></div>
                    </div>
                </a>
            @empty
                <x-empty icon="users" title="Разделы ещё не созданы" text="Администратор скоро всё подготовит." />
            @endforelse
        </div>

        <aside class="card h-fit p-6">
            <h2 class="font-sans text-sm font-bold uppercase tracking-wider text-ink-400">Свежие темы</h2>
            <div class="mt-4 space-y-4">
                @forelse($recent as $t)
                    <a href="{{ route('forum.topic', $t) }}" class="group block">
                        <p class="line-clamp-2 text-sm font-bold group-hover:text-ember-300">{{ $t->title }}</p>
                        <p class="mt-1 text-xs text-ink-400">{{ $t->user?->name ?? 'Гость' }} · {{ $t->category?->name }} · {{ $t->replies_count }} отв.</p>
                    </a>
                @empty
                    <p class="text-sm text-ink-400">Тем пока нет — создайте первую!</p>
                @endforelse
            </div>
        </aside>
    </div>
</div>
