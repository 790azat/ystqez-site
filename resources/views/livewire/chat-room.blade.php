<div @class(['fixed right-5 z-50 flex flex-col items-end gap-3' => $mode === 'widget',
    'bottom-24 lg:bottom-5' => $mode === 'widget' && $raised,
    'bottom-5' => $mode === 'widget' && ! $raised, 'container-yq pt-10' => $mode !== 'widget'])>
@if($mode === 'widget')
    @if($open)
        <div class="flex h-[30rem] max-h-[75vh] w-[calc(100vw-2.5rem)] flex-col overflow-hidden rounded-3xl bg-ink-850/95 shadow-2xl ring-1 ring-white/10 backdrop-blur-xl sm:w-96 animate-fade-up">
            <div class="flex items-center justify-between border-b border-white/5 px-5 py-3.5">
                <div class="flex items-center gap-2">
                    <span class="size-2 rounded-full bg-emerald-400 animate-pulse-dot"></span>
                    <span class="font-display text-sm font-semibold">{{ __('Чат Yst Qez') }}</span>
                </div>
                <div class="flex items-center gap-1">
                    <a href="{{ route('chat') }}" class="grid size-8 place-items-center rounded-full text-ink-400 hover:bg-white/5 hover:text-white" title="{{ __('Открыть на весь экран') }}"><x-icon name="external" class="size-4"/></a>
                    <button wire:click="toggle" class="grid size-8 place-items-center rounded-full text-ink-400 hover:bg-white/5 hover:text-white" title="{{ __('Закрыть') }}"><x-icon name="x" class="size-4"/></button>
                </div>
            </div>
            <div wire:poll.4s class="flex-1 overflow-y-auto px-4 py-3"
                 x-data x-init="$el.scrollTop = $el.scrollHeight; new MutationObserver(() => { if ($el.scrollHeight - $el.scrollTop - $el.clientHeight < 160) $el.scrollTop = $el.scrollHeight }).observe($el, { childList: true, subtree: true })">
                @include('livewire.partials.chat-messages', ['messages' => $messages, 'compact' => true])
            </div>
            @include('livewire.partials.chat-input', ['compact' => true])
        </div>
    @endif
    <button wire:click="toggle" class="group grid size-14 place-items-center rounded-full bg-gradient-to-br from-ember-500 to-sun-500 text-ink-950 shadow-xl shadow-ember-500/30 transition hover:scale-105" aria-label="{{ $open ? __('Закрыть чат') : __('Открыть чат') }}">
        <x-icon :name="$open ? 'x' : 'chat'" class="size-6"/>
    </button>
@else
    <div class="mb-8">
        <p class="kicker"><span class="size-2 rounded-full bg-emerald-400 animate-pulse-dot"></span>Live</p>
        <h1 class="mt-2 text-4xl font-semibold sm:text-5xl">{{ __('Живой чат') }}</h1>
        <p class="mt-3 max-w-xl text-ink-400">{{ __('Обсуждаем выпуски, делимся мыслями и просто болтаем. Сообщения обновляются автоматически.') }}</p>
    </div>
    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
        <div class="card flex h-[70vh] min-h-[28rem] flex-col overflow-hidden">
            <div wire:poll.3s class="flex-1 overflow-y-auto p-5 sm:p-6"
                 x-data x-init="$el.scrollTop = $el.scrollHeight; new MutationObserver(() => { if ($el.scrollHeight - $el.scrollTop - $el.clientHeight < 200) $el.scrollTop = $el.scrollHeight }).observe($el, { childList: true, subtree: true })">
                @include('livewire.partials.chat-messages', ['messages' => $messages, 'compact' => false])
            </div>
            @include('livewire.partials.chat-input', ['compact' => false])
        </div>
        <aside class="space-y-4">
            <div class="card p-6">
                <h2 class="font-sans text-sm font-bold uppercase tracking-wider text-ink-400">{{ __('Правила') }}</h2>
                <p class="mt-3 text-sm leading-relaxed text-ink-300">{{ setting('chat_rules') }}</p>
            </div>
            <div class="card p-6">
                <h2 class="font-sans text-sm font-bold uppercase tracking-wider text-ink-400">{{ __('Вы в чате как') }}</h2>
                @if($hasNick)
                    <div class="mt-3 flex items-center gap-3">
                        <span class="avatar size-10">{{ mb_strtoupper(mb_substr(auth()->user()?->name ?? session('guest_nickname'), 0, 1)) }}</span>
                        <b>{{ auth()->user()?->name ?? session('guest_nickname') }}</b>
                    </div>
                @else
                    <p class="mt-3 text-sm text-ink-400">{{ __('Пока анонимно — укажите ник внизу чата.') }}</p>
                @endif
                @guest<p class="mt-4 text-xs text-ink-400">{{ __('Хотите постоянное имя?') }} <a href="{{ route('register') }}" class="link">{{ __('Зарегистрируйтесь') }}</a></p>@endguest
            </div>
            <a href="{{ route('forum.index') }}" class="card card-hover block p-6">
                <p class="font-display font-semibold">{{ __('Долгий разговор?') }}</p>
                <p class="mt-1 text-sm text-ink-400">{{ __('Для обстоятельных дискуссий есть форум →') }}</p>
            </a>
        </aside>
    </div>
@endif
</div>
