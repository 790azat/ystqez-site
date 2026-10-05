<div class="space-y-3">
    @forelse($messages as $m)
        @php($mine = (auth()->id() && $m->user_id === auth()->id()) || (!auth()->id() && !$m->user_id && $m->nickname === session('guest_nickname')))
        <div wire:key="m{{ $m->id }}" @class(['group flex gap-2.5', 'flex-row-reverse' => $mine])>
            @unless($compact)<span class="avatar size-8 text-xs">{{ mb_strtoupper(mb_substr($m->nickname, 0, 1)) }}</span>@endunless
            <div @class(['max-w-[80%] rounded-2xl px-3.5 py-2', 'rounded-tr-sm bg-ember-500/15 ring-1 ring-ember-500/20' => $mine, 'rounded-tl-sm bg-white/[.05]' => !$mine])>
                <div class="flex items-baseline gap-2">
                    <span class="text-xs font-bold {{ $mine ? 'text-ember-300' : 'text-sun-400' }}">{{ $m->nickname }}</span>
                    <span class="text-[10px] text-ink-400">{{ $m->created_at->format('H:i') }}</span>
                    @if(auth()->user()?->is_admin)
                        <button wire:click="delete({{ $m->id }})" class="text-ink-400 opacity-0 transition hover:text-red-400 group-hover:opacity-100" title="Удалить"><x-icon name="trash" class="size-3"/></button>
                    @endif
                </div>
                <p class="break-words text-sm leading-relaxed text-ink-100">{{ $m->body }}</p>
            </div>
        </div>
    @empty
        <div class="grid h-full place-items-center py-16 text-center">
            <div>
                <x-icon name="chat" class="mx-auto size-10 text-ink-600"/>
                <p class="mt-3 text-sm text-ink-400">Тут пока тихо. Напишите первое сообщение!</p>
            </div>
        </div>
    @endforelse
</div>
