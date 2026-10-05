<div class="container-yq max-w-4xl pt-10">
    <nav class="mb-5 flex flex-wrap items-center gap-2 text-sm text-ink-400">
        <a href="{{ route('forum.index') }}" class="hover:text-white">Форум</a><span>/</span>
        <a href="{{ route('forum.category', $topic->category) }}" class="hover:text-white">{{ $topic->category->name }}</a>
    </nav>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="mb-2 flex gap-2">
                @if($topic->is_pinned)<span class="badge bg-sun-500/15 text-sun-400"><x-icon name="pin" class="size-3"/> Закреплено</span>@endif
                @if($topic->is_locked)<span class="badge bg-white/5 text-ink-300"><x-icon name="lock" class="size-3"/> Закрыто</span>@endif
            </div>
            <h1 class="text-3xl font-semibold leading-tight">{{ $topic->title }}</h1>
            <p class="mt-2 text-sm text-ink-400">{{ $topic->replies_count }} {{ \App\Support\Ru::plural($topic->replies_count, ['ответ', 'ответа', 'ответов']) }} · {{ $topic->views }} {{ \App\Support\Ru::plural($topic->views, ['просмотр', 'просмотра', 'просмотров']) }}</p>
        </div>
        @if(auth()->user()?->is_admin)
            <div class="flex shrink-0 gap-2">
                <button wire:click="togglePin" class="btn btn-ghost btn-sm"><x-icon name="pin" class="size-3.5"/> {{ $topic->is_pinned ? 'Открепить' : 'Закрепить' }}</button>
                <button wire:click="toggleLock" class="btn btn-ghost btn-sm"><x-icon name="lock" class="size-3.5"/> {{ $topic->is_locked ? 'Открыть' : 'Закрыть' }}</button>
            </div>
        @endif
    </div>

    <div class="mt-8 space-y-4">
        @foreach($posts as $post)
            <article wire:key="p{{ $post->id }}" @class(['card p-5 sm:p-6', 'ring-ember-500/25' => $post->id === $firstPostId])>
                <header class="flex items-center gap-3">
                    <span class="avatar size-10 text-sm">{{ $post->user?->initials() ?? '?' }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="flex items-center gap-2 text-sm font-bold">
                            {{ $post->user?->name ?? 'Удалённый пользователь' }}
                            @if($post->user?->is_admin)<span class="badge bg-ember-500/15 text-ember-300">Yst Qez</span>@endif
                            @if($post->id === $firstPostId)<span class="badge bg-white/5 text-ink-400">автор</span>@endif
                        </p>
                        <p class="text-xs text-ink-400">{{ $post->created_at->translatedFormat('j F Y, H:i') }}</p>
                    </div>
                    @if(auth()->user()?->is_admin)
                        <button wire:click="deletePost({{ $post->id }})" wire:confirm="{{ $post->id === $firstPostId ? 'Удалить всю тему?' : 'Удалить сообщение?' }}" class="text-ink-400 hover:text-red-400" title="Удалить"><x-icon name="trash" class="size-4"/></button>
                    @endif
                </header>
                <div class="mt-4 whitespace-pre-line break-words leading-relaxed text-ink-200">{{ $post->body }}</div>
            </article>
        @endforeach
    </div>
    {{ $posts->links() }}

    <div class="mt-10">
        @auth
            @if($topic->is_locked && !auth()->user()->is_admin)
                <div class="card flex items-center gap-3 p-5 text-sm text-ink-400"><x-icon name="lock" class="size-5"/> Тема закрыта — новые ответы не принимаются.</div>
            @else
                <form wire:submit="reply" class="card p-5 sm:p-6">
                    <label class="label" for="reply">Ваш ответ</label>
                    <textarea id="reply" wire:model="body" rows="5" class="input" placeholder="Напишите, что думаете…"></textarea>
                    @error('body')<p class="error">{{ $message }}</p>@enderror
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="reply"><x-icon name="reply" class="size-4"/> Ответить</button>
                    </div>
                </form>
            @endif
        @else
            <div class="card flex flex-col items-center gap-4 p-8 text-center">
                <p class="text-ink-300">Чтобы ответить, войдите или зарегистрируйтесь.</p>
                <div class="flex gap-2">
                    <a href="{{ route('login') }}" class="btn btn-ghost">Войти</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Регистрация</a>
                </div>
            </div>
        @endauth
    </div>
</div>
