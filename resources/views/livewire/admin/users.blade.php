<div>
    @include('partials.admin-flash')
    <h1 class="text-3xl font-semibold">Пользователи</h1>
    <div class="relative mt-6 max-w-sm">
        <x-icon name="search" class="pointer-events-none absolute left-4 top-1/2 size-4 -translate-y-1/2 text-ink-400"/>
        <input type="search" wire:model.live.debounce.300ms="search" class="input pl-11" placeholder="Имя или email">
    </div>
    <div class="card mt-4 overflow-x-auto">
        <table class="table-yq">
            <thead><tr><th>Пользователь</th><th>Email</th><th class="text-right">Темы / ответы</th><th>Регистрация</th><th>Роль</th><th></th></tr></thead>
            <tbody>
            @foreach($users as $u)
                <tr wire:key="u{{ $u->id }}">
                    <td><div class="flex items-center gap-3"><span class="avatar size-8 text-xs">{{ $u->initials() }}</span><b>{{ $u->name }}</b></div></td>
                    <td class="text-ink-300">{{ $u->email }}</td>
                    <td class="text-right tabular-nums">{{ $u->topics_count }} / {{ $u->posts_count }}</td>
                    <td class="whitespace-nowrap text-ink-400">{{ $u->created_at?->format('d.m.Y') }}</td>
                    <td>
                        <button wire:click="toggleAdmin({{ $u->id }})" @class(['badge', 'bg-ember-500/15 text-ember-300' => $u->is_admin, 'bg-white/5 text-ink-400 hover:text-white' => !$u->is_admin])>{{ $u->is_admin ? 'Админ' : 'Пользователь' }}</button>
                    </td>
                    <td class="text-right">
                        @if($u->id !== auth()->id())
                            <button wire:click="delete({{ $u->id }})" wire:confirm="Удалить пользователя {{ $u->name }}?" class="grid size-8 place-items-center rounded-lg text-ink-400 hover:bg-red-500/10 hover:text-red-400"><x-icon name="trash" class="size-4"/></button>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{ $users->links() }}
</div>
