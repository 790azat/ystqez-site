<div>
    @include('partials.admin-flash')
    <h1 class="text-3xl font-semibold">Форум</h1>
    <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
        <div class="card overflow-x-auto">
            <table class="table-yq">
                <thead><tr><th>Тема</th><th>Раздел</th><th class="text-right">Ответы</th><th></th></tr></thead>
                <tbody>
                @forelse($topics as $t)
                    <tr wire:key="t{{ $t->id }}">
                        <td>
                            <a href="{{ route('forum.topic', $t) }}" target="_blank" class="font-semibold hover:text-ember-300">{{ $t->title }}</a>
                            <p class="text-xs text-ink-400">{{ $t->user?->name ?? '—' }} · {{ $t->last_post_at?->diffForHumans() }}</p>
                        </td>
                        <td class="text-ink-400">{{ $t->category?->name }}</td>
                        <td class="text-right tabular-nums">{{ $t->replies_count }}</td>
                        <td>
                            <div class="flex justify-end gap-1">
                                <button wire:click="togglePin({{ $t->id }})" title="Закрепить" @class(['grid size-8 place-items-center rounded-lg hover:bg-white/5', 'text-sun-400' => $t->is_pinned, 'text-ink-600' => !$t->is_pinned])><x-icon name="pin" class="size-4"/></button>
                                <button wire:click="toggleLock({{ $t->id }})" title="Закрыть" @class(['grid size-8 place-items-center rounded-lg hover:bg-white/5', 'text-ember-300' => $t->is_locked, 'text-ink-600' => !$t->is_locked])><x-icon name="lock" class="size-4"/></button>
                                <button wire:click="deleteTopic({{ $t->id }})" wire:confirm="Удалить тему со всеми ответами?" class="grid size-8 place-items-center rounded-lg text-ink-400 hover:bg-red-500/10 hover:text-red-400"><x-icon name="trash" class="size-4"/></button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-12 text-center text-ink-400">Тем пока нет</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="px-4 pb-4">{{ $topics->links() }}</div>
        </div>

        <div class="space-y-4">
            <div class="card p-5">
                <h2 class="font-display font-semibold">Разделы</h2>
                <div class="mt-3 divide-y divide-white/5">
                    @foreach($categories as $cat)
                        <div class="flex items-center gap-3 py-2.5 text-sm" wire:key="cat{{ $cat->id }}">
                            <span class="text-lg">{{ $cat->emoji }}</span>
                            <span class="flex-1">{{ $cat->name }} <span class="text-ink-400">({{ $cat->topics_count }})</span></span>
                            <button wire:click="deleteCategory({{ $cat->id }})" wire:confirm="Удалить раздел и ВСЕ его темы?" class="text-ink-400 hover:text-red-400"><x-icon name="trash" class="size-4"/></button>
                        </div>
                    @endforeach
                </div>
            </div>
            <form wire:submit="addCategory" class="card space-y-3 p-5">
                <h2 class="font-display font-semibold">Новый раздел</h2>
                <div class="grid grid-cols-[70px_1fr] gap-2">
                    <input type="text" wire:model="catEmoji" class="input text-center" maxlength="4">
                    <input type="text" wire:model="catName" class="input" placeholder="Название">
                </div>
                @error('catName')<p class="error">{{ $message }}</p>@enderror
                <input type="text" wire:model="catDescription" class="input" placeholder="Описание">
                <button class="btn btn-primary w-full"><x-icon name="plus" class="size-4"/> Добавить</button>
            </form>
        </div>
    </div>
</div>
