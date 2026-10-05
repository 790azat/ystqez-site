<div>
    @include('partials.admin-flash')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-3xl font-semibold">Видео</h1>
        <div class="flex flex-wrap gap-2">
            <button wire:click="sync" wire:loading.attr="disabled" wire:target="sync" class="btn btn-ghost">
                <x-icon name="refresh" class="size-4" wire:loading.class="animate-spin" wire:target="sync"/> Синхронизировать с YouTube
            </button>
            <button wire:click="create" class="btn btn-primary"><x-icon name="plus" class="size-4"/> Добавить видео</button>
        </div>
    </div>

    @if($showForm)
        <form wire:submit="save" class="card mt-6 grid gap-4 p-6 md:grid-cols-2">
            <h2 class="font-display text-lg font-semibold md:col-span-2">{{ $editingId ? 'Редактировать видео' : 'Новое видео' }}</h2>
            <div class="md:col-span-2">
                <label class="label">Ссылка YouTube или ID *</label>
                <input type="text" wire:model="form.url" class="input" placeholder="https://www.youtube.com/watch?v=…">
                @error('form.url')<p class="error">{{ $message }}</p>@enderror
                @unless($editingId)<p class="mt-1.5 text-xs text-ink-400">Название и обложка подтянутся автоматически через YouTube oEmbed, если оставить их пустыми.</p>@endunless
            </div>
            <div class="md:col-span-2">
                <label class="label">Название</label>
                <input type="text" wire:model="form.title" class="input">
                @error('form.title')<p class="error">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="label">Описание (таймкоды вида 12:34 станут кликабельными)</label>
                <textarea wire:model="form.description" rows="6" class="input"></textarea>
            </div>
            <div>
                <label class="label">Тип</label>
                <select wire:model="form.type" class="input">
                    @foreach(\App\Models\Video::TYPES as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="label">Дата публикации</label>
                <input type="datetime-local" wire:model="form.published_at" class="input">
                @error('form.published_at')<p class="error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label">Длительность (сек)</label>
                <input type="number" min="0" wire:model="form.duration" class="input">
                @error('form.duration')<p class="error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label">Просмотры</label>
                <input type="number" min="0" wire:model="form.view_count" class="input">
                @error('form.view_count')<p class="error">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="label">URL обложки (внешняя ссылка, необязательно)</label>
                <input type="url" wire:model="form.thumbnail" class="input" placeholder="https://i.ytimg.com/vi/…/maxresdefault.jpg">
                @error('form.thumbnail')<p class="error">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="label">Теги через запятую</label>
                <input type="text" wire:model="form.tags" class="input" placeholder="философия, путешествия, книги">
            </div>
            <div class="flex flex-wrap gap-6 md:col-span-2">
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="form.is_featured" class="size-4 accent-ember-500"> Избранное (показывать на главной)</label>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="form.is_published" class="size-4 accent-ember-500"> Опубликовано</label>
            </div>
            <div class="flex justify-end gap-2 md:col-span-2">
                <button type="button" wire:click="cancel" class="btn btn-ghost">Отмена</button>
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">Сохранить</button>
            </div>
        </form>
    @endif

    <div class="relative mt-6 max-w-sm">
        <x-icon name="search" class="pointer-events-none absolute left-4 top-1/2 size-4 -translate-y-1/2 text-ink-400"/>
        <input type="search" wire:model.live.debounce.300ms="search" class="input pl-11" placeholder="Поиск по названию или ID">
    </div>

    <div class="card mt-4 overflow-x-auto">
        <table class="table-yq">
            <thead><tr><th>Видео</th><th>Тип</th><th>Дата</th><th class="text-right">Просмотры</th><th class="text-right">♥ сайт</th><th></th></tr></thead>
            <tbody>
            @forelse($videos as $v)
                <tr wire:key="v{{ $v->id }}">
                    <td>
                        <div class="flex items-center gap-3">
                            <img src="{{ $v->thumb_url }}" data-fallback="{{ $v->fallback_thumb }}" class="h-10 w-16 shrink-0 rounded-lg object-cover" alt="" loading="lazy" referrerpolicy="no-referrer">
                            <div class="min-w-0">
                                <a href="{{ route('videos.show', $v->slug) }}" target="_blank" class="line-clamp-1 max-w-md font-semibold hover:text-ember-300">{{ $v->title }}</a>
                                <p class="font-mono text-xs text-ink-400">{{ $v->youtube_id }}</p>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-white/5 text-ink-300">{{ $v->type_label }}</span></td>
                    <td class="whitespace-nowrap text-ink-400">{{ $v->published_at?->format('d.m.Y') }}</td>
                    <td class="text-right tabular-nums">{{ number_format($v->view_count, 0, ',', ' ') }}</td>
                    <td class="text-right tabular-nums">{{ $v->site_likes }}</td>
                    <td>
                        <div class="flex justify-end gap-1">
                            <button wire:click="toggleFeatured({{ $v->id }})" title="Избранное" @class(['grid size-8 place-items-center rounded-lg hover:bg-white/5', 'text-sun-400' => $v->is_featured, 'text-ink-600' => !$v->is_featured])><x-icon name="star" class="size-4"/></button>
                            <button wire:click="togglePublished({{ $v->id }})" title="{{ $v->is_published ? 'Скрыть' : 'Опубликовать' }}" @class(['grid size-8 place-items-center rounded-lg hover:bg-white/5', 'text-emerald-400' => $v->is_published, 'text-ink-600' => !$v->is_published])><x-icon name="eye" class="size-4"/></button>
                            <button wire:click="edit({{ $v->id }})" title="Редактировать" class="grid size-8 place-items-center rounded-lg text-ink-300 hover:bg-white/5"><x-icon name="edit" class="size-4"/></button>
                            <button wire:click="delete({{ $v->id }})" wire:confirm="Удалить видео «{{ $v->title }}»?" title="Удалить" class="grid size-8 place-items-center rounded-lg text-ink-400 hover:bg-red-500/10 hover:text-red-400"><x-icon name="trash" class="size-4"/></button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-12 text-center text-ink-400">Видео нет. Добавьте вручную или синхронизируйте с YouTube.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $videos->links() }}
</div>
