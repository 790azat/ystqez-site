<div class="border-t border-white/5 {{ $compact ? 'p-3' : 'p-4' }}">
    @if($hasNick)
        <form wire:submit="send" class="flex items-center gap-2">
            <input type="text" wire:model="body" maxlength="500" placeholder="Сообщение…" class="input {{ $compact ? 'py-2.5' : '' }}" autocomplete="off" aria-label="Сообщение"
                   x-on:chat-sent.window="$el.value = ''; $el.focus()">
            <input type="text" wire:model="website" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true">
            <button type="submit" class="btn btn-primary shrink-0 !px-3.5" wire:loading.attr="disabled" wire:target="send" aria-label="Отправить"><x-icon name="send" class="size-4"/></button>
        </form>
        @error('body')<p class="error">{{ $message }}</p>@enderror
    @else
        <form wire:submit="setNickname" class="flex items-center gap-2">
            <input type="text" wire:model="nickname" maxlength="30" placeholder="Как вас зовут?" class="input {{ $compact ? 'py-2.5' : '' }}" autocomplete="nickname" aria-label="Ник">
            <button type="submit" class="btn btn-primary shrink-0">Войти</button>
        </form>
        @error('nickname')<p class="error">{{ $message }}</p>@enderror
        <p class="mt-2 text-[11px] text-ink-400">Или <a href="{{ route('login') }}" class="link">войдите в аккаунт</a></p>
    @endif
</div>
