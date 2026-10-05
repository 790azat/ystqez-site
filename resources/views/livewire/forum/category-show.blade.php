<div class="container-yq pt-10">
    <nav class="mb-5 flex items-center gap-2 text-sm text-ink-400"><a href="{{ route('forum.index') }}" class="hover:text-white">Форум</a><span>/</span><span class="text-ink-300">{{ $category->name }}</span></nav>
    <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
        <div class="flex items-center gap-4">
            <span class="grid size-16 place-items-center rounded-2xl bg-white/5 text-3xl ring-1 ring-white/10">{{ $category->emoji ?: '💬' }}</span>
            <div>
                <h1 class="text-3xl font-semibold sm:text-4xl">{{ $category->name }}</h1>
                @if($category->description)<p class="mt-1 text-ink-400">{{ $category->description }}</p>@endif
            </div>
        </div>
        @unless($showForm)
            <button wire:click="openForm" class="btn btn-primary"><x-icon name="plus" class="size-4"/> Новая тема</button>
        @endunless
    </div>

    @if($showForm)
        <form wire:submit="create" class="card mt-8 space-y-4 p-6 animate-fade-up">
            <h2 class="font-display text-lg font-semibold">Новая тема</h2>
            <div>
                <input type="text" wire:model="title" class="input text-base font-semibold" placeholder="Заголовок темы" maxlength="200">
                @error('title')<p class="error">{{ $message }}</p>@enderror
            </div>
            <div>
                <textarea wire:model="body" rows="7" class="input" placeholder="Расскажите подробнее…"></textarea>
                @error('body')<p class="error">{{ $message }}</p>@enderror
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" wire:click="$set('showForm', false)" class="btn btn-ghost">Отмена</button>
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"><x-icon name="send" class="size-4"/> Опубликовать</button>
            </div>
        </form>
    @endif

    <div class="mt-8">
        @if($topics->isNotEmpty())
            <div class="card divide-y divide-white/5 overflow-hidden">
                @foreach($topics as $t)
                    <a href="{{ route('forum.topic', $t) }}" wire:key="t{{ $t->id }}" class="group flex items-center gap-4 p-5 transition hover:bg-white/[.02]">
                        <span class="avatar size-10 text-sm">{{ $t->user?->initials() ?? '?' }}</span>
                        <div class="min-w-0 flex-1">
                            <h3 class="flex items-center gap-2 font-sans font-bold group-hover:text-white">
                                @if($t->is_pinned)<x-icon name="pin" class="size-4 shrink-0 text-sun-400"/>@endif
                                @if($t->is_locked)<x-icon name="lock" class="size-4 shrink-0 text-ink-400"/>@endif
                                <span class="truncate">{{ $t->title }}</span>
                            </h3>
                            <p class="mt-0.5 text-xs text-ink-400">{{ $t->user?->name ?? 'Удалённый пользователь' }} · {{ $t->created_at->translatedFormat('j M Y') }}</p>
                        </div>
                        <div class="hidden shrink-0 gap-5 text-center text-xs text-ink-400 sm:flex">
                            <span class="inline-flex items-center gap-1"><x-icon name="reply" class="size-3.5"/> {{ $t->replies_count }}</span>
                            <span class="inline-flex items-center gap-1"><x-icon name="eye" class="size-3.5"/> {{ $t->views }}</span>
                        </div>
                        <span class="hidden w-28 shrink-0 text-right text-xs text-ink-400 md:block">{{ $t->last_post_at?->diffForHumans() }}</span>
                    </a>
                @endforeach
            </div>
            {{ $topics->links() }}
        @else
            <x-empty icon="chat" title="В этом разделе пока нет тем" text="Станьте первым, кто начнёт разговор.">
                <button wire:click="openForm" class="btn btn-primary"><x-icon name="plus" class="size-4"/> Создать тему</button>
            </x-empty>
        @endif
    </div>
</div>
