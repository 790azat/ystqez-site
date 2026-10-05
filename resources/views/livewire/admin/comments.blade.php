<div>
    @include('partials.admin-flash')
    <h1 class="text-3xl font-semibold">Комментарии к видео</h1>
    <div class="mt-6 space-y-3">
        @forelse($comments as $c)
            <div class="card flex gap-4 p-5" wire:key="c{{ $c->id }}">
                <span class="avatar size-10 text-sm">{{ mb_strtoupper(mb_substr($c->author_name, 0, 1)) }}</span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm"><b>{{ $c->author_name }}</b> @unless($c->user_id)<span class="badge bg-white/5 text-ink-400">гость</span>@endunless <span class="text-ink-400">· {{ $c->created_at->diffForHumans() }} · {{ $c->ip }}</span></p>
                    @if($c->video)<a href="{{ route('videos.show', $c->video->slug) }}#comments" target="_blank" class="text-xs text-ember-300 hover:underline">{{ $c->video->title }}</a>@endif
                    <p class="mt-2 whitespace-pre-line break-words text-sm text-ink-200">{{ $c->body }}</p>
                </div>
                <button wire:click="delete({{ $c->id }})" wire:confirm="Удалить комментарий?" class="grid size-8 shrink-0 place-items-center rounded-lg text-ink-400 hover:bg-red-500/10 hover:text-red-400"><x-icon name="trash" class="size-4"/></button>
            </div>
        @empty
            <x-empty icon="reply" title="Комментариев нет" />
        @endforelse
    </div>
    {{ $comments->links() }}
</div>
