<section class="mt-10" id="comments">
    <h2 class="flex items-center gap-3 text-xl font-semibold">
        Обсуждение <span class="rounded-full bg-white/5 px-2.5 py-0.5 font-sans text-sm text-ink-300">{{ $total }}</span>
    </h2>

    <form wire:submit="post" class="card mt-5 p-5">
        <div class="flex gap-3">
            <span class="avatar size-10 text-sm">{{ auth()->check() ? auth()->user()->initials() : ($name ? mb_strtoupper(mb_substr($name, 0, 1)) : '?') }}</span>
            <div class="flex-1 space-y-3">
                @guest
                    <div>
                        <input type="text" wire:model.blur="name" class="input" placeholder="Ваше имя" maxlength="40" autocomplete="nickname">
                        @error('name')<p class="error">{{ $message }}</p>@enderror
                    </div>
                @endguest
                <div>
                    <textarea wire:model="body" rows="3" class="input" placeholder="Что думаете о выпуске?" maxlength="2000"></textarea>
                    @error('body')<p class="error">{{ $message }}</p>@enderror
                </div>
                <input type="text" wire:model="website" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-xs text-ink-400">@guest Можно без регистрации. <a href="{{ route('login') }}" class="link">Войти</a>@else Вы пишете как <b class="text-ink-200">{{ auth()->user()->name }}</b>@endguest</p>
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="post">
                        <x-icon name="send" class="size-4"/> Отправить
                    </button>
                </div>
            </div>
        </div>
    </form>

    <div class="mt-6 space-y-4">
        @forelse($comments as $c)
            <div class="flex gap-3" wire:key="c{{ $c->id }}">
                <span class="avatar size-10 text-sm">{{ mb_strtoupper(mb_substr($c->author_name, 0, 1)) }}</span>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 text-sm">
                        <b>{{ $c->author_name }}</b>
                        @if($c->user?->is_admin)<span class="badge bg-ember-500/15 text-ember-300">Yst Qez</span>@endif
                        <span class="text-xs text-ink-400">{{ $c->created_at->diffForHumans() }}</span>
                        @if(auth()->user()?->is_admin)
                            <button wire:click="delete({{ $c->id }})" wire:confirm="Удалить комментарий?" class="ml-auto text-ink-400 hover:text-red-400" title="Удалить"><x-icon name="trash" class="size-4"/></button>
                        @endif
                    </div>
                    <p class="mt-1 whitespace-pre-line break-words text-[15px] leading-relaxed text-ink-200">{{ $c->body }}</p>
                </div>
            </div>
        @empty
            <p class="rounded-2xl bg-white/[.02] p-6 text-center text-sm text-ink-400">Комментариев пока нет — начните разговор первым.</p>
        @endforelse
    </div>
    @if($total > $comments->count())
        <button wire:click="more" class="btn btn-ghost mt-6 w-full">Показать ещё</button>
    @endif
</section>
