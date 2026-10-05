<button type="button" wire:click="toggle" wire:loading.attr="disabled"
        @class(['btn', 'btn-primary' => $liked, 'btn-ghost' => !$liked])
        aria-pressed="{{ $liked ? 'true' : 'false' }}" title="{{ $liked ? 'Убрать лайк' : 'Нравится' }}">
    <x-icon :name="$liked ? 'heart-fill' : 'heart'" class="size-4 {{ $liked ? '' : 'text-ember-400' }}"/>
    <span>{{ $liked ? 'Нравится' : 'Лайк' }}</span>
    <span class="rounded-full {{ $liked ? 'bg-black/15' : 'bg-white/10' }} px-2 py-0.5 text-xs">{{ $count }}</span>
</button>
