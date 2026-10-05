<div>
    @include('partials.admin-flash')
    <h1 class="text-3xl font-semibold">{{ __('Заявки на сотрудничество') }}</h1>
    <div class="mt-6 flex flex-wrap gap-2">
        <button wire:click="$set('status', '')" @class(['chip', 'chip-active' => $status === ''])>{{ __('Все') }} <span class="opacity-60">{{ $counts->sum() }}</span></button>
        @foreach(\App\Models\CollabRequest::STATUSES as $k => $l)
            <button wire:click="$set('status', '{{ $k }}')" @class(['chip', 'chip-active' => $status === $k])>{{ __($l) }} <span class="opacity-60">{{ $counts[$k] ?? 0 }}</span></button>
        @endforeach
    </div>

    <div class="mt-6 space-y-3">
        @forelse($items as $c)
            <div class="card overflow-hidden" wire:key="c{{ $c->id }}">
                <button wire:click="open({{ $c->id }})" class="flex w-full items-center gap-4 p-5 text-left hover:bg-white/[.02]">
                    <span @class(['size-2.5 shrink-0 rounded-full', 'bg-ember-500' => $c->status === 'new', 'bg-sun-500' => $c->status === 'in_progress', 'bg-emerald-500' => $c->status === 'done'])></span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-bold">{{ $c->name }}@if($c->company) <span class="font-normal text-ink-400">· {{ $c->company }}</span>@endif</p>
                        <p class="truncate text-xs text-ink-400">{{ $c->type_label }} · {{ $c->contact }} · {{ fdate($c->created_at, 'short_datetime') }}</p>
                    </div>
                    <span class="badge bg-white/5 text-ink-300">{{ $c->status_label }}</span>
                    <x-icon name="chevron-down" class="size-4 text-ink-400 transition {{ $openId === $c->id ? 'rotate-180' : '' }}"/>
                </button>
                @if($openId === $c->id)
                    <div class="border-t border-white/5 p-5">
                        <dl class="grid gap-4 text-sm sm:grid-cols-4">
                            <div><dt class="label">{{ __('Контакт') }}</dt><dd class="break-all">{{ $c->contact }}</dd></div>
                            <div><dt class="label">{{ __('Тип') }}</dt><dd>{{ $c->type_label }}</dd></div>
                            <div><dt class="label">{{ __('Бюджет') }}</dt><dd>{{ $c->budget ?: '—' }}</dd></div>
                            <div><dt class="label">IP</dt><dd class="font-mono text-xs">{{ $c->ip }}</dd></div>
                        </dl>
                        <div class="mt-4 whitespace-pre-line rounded-2xl bg-white/[.03] p-4 text-sm leading-relaxed">{{ $c->message }}</div>
                        <div class="mt-4">
                            <label class="label">{{ __('Заметка (видна только админам)') }}</label>
                            <textarea wire:model="note" rows="2" class="input"></textarea>
                        </div>
                        <div class="mt-4 flex flex-wrap items-center gap-2">
                            @foreach(\App\Models\CollabRequest::STATUSES as $k => $l)
                                <button wire:click="setStatus({{ $c->id }}, '{{ $k }}')" @class(['btn btn-sm', 'btn-primary' => $c->status === $k, 'btn-ghost' => $c->status !== $k])>{{ __($l) }}</button>
                            @endforeach
                            <button wire:click="saveNote({{ $c->id }})" class="btn btn-ghost btn-sm">{{ __('Сохранить заметку') }}</button>
                            @if(filter_var($c->contact, FILTER_VALIDATE_EMAIL))<a href="mailto:{{ $c->contact }}" class="btn btn-ghost btn-sm"><x-icon name="mail" class="size-3.5"/> {{ __('Ответить') }}</a>@endif
                            <button wire:click="delete({{ $c->id }})" wire:confirm="{{ __('Удалить заявку?') }}" class="btn btn-danger btn-sm ml-auto"><x-icon name="trash" class="size-3.5"/> {{ __('Удалить') }}</button>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <x-empty icon="inbox" title="{{ __('Заявок нет') }}" text="{{ __('Когда кто-то заполнит форму сотрудничества, заявка появится здесь.') }}" />
        @endforelse
    </div>
    {{ $items->links() }}
</div>
