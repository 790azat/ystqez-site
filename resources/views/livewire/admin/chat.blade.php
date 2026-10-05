<div>
    @include('partials.admin-flash')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-3xl font-semibold">{{ __('Модерация чата') }}</h1>
        <button wire:click="purgeOld" wire:confirm="{{ __('Удалить все сообщения старше 30 дней?') }}" class="btn btn-danger"><x-icon name="trash" class="size-4"/> {{ __('Очистить старше 30 дней') }}</button>
    </div>
    <div class="card mt-6 overflow-x-auto" wire:poll.10s>
        <table class="table-yq">
            <thead><tr><th>{{ __('Автор') }}</th><th>{{ __('Сообщение') }}</th><th>{{ __('Время') }}</th><th>IP</th><th></th></tr></thead>
            <tbody>
            @forelse($messages as $m)
                <tr wire:key="m{{ $m->id }}">
                    <td class="whitespace-nowrap font-semibold">{{ $m->nickname }} @if($m->user_id)<span class="badge bg-white/5 text-ink-400">user</span>@endif</td>
                    <td class="max-w-lg break-words text-ink-200">{{ $m->body }}</td>
                    <td class="whitespace-nowrap text-ink-400">{{ $m->created_at->format('d.m H:i') }}</td>
                    <td class="font-mono text-xs text-ink-400">{{ $m->ip }}</td>
                    <td class="text-right"><button wire:click="delete({{ $m->id }})" class="grid size-8 place-items-center rounded-lg text-ink-400 hover:bg-red-500/10 hover:text-red-400"><x-icon name="trash" class="size-4"/></button></td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-12 text-center text-ink-400">{{ __('Сообщений нет') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $messages->links() }}
</div>
