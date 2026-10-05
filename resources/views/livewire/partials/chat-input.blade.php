<div class="border-t border-white/5 {{ $compact ? 'p-3' : 'p-4' }}">
    @if($hasNick)
        <form wire:submit="send" class="flex items-center gap-2">
            <input type="text" wire:model="body" maxlength="500" placeholder="{{ __('Сообщение…') }}" class="input {{ $compact ? 'py-2.5' : '' }}" autocomplete="off" aria-label="{{ __('Сообщение') }}"
                   x-on:chat-sent.window="$el.value = ''; $el.focus()">
            <input type="text" wire:model="website" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true">
            <button type="submit" class="btn btn-primary shrink-0 !px-3.5" wire:loading.attr="disabled" wire:target="send" aria-label="{{ __('Отправить') }}"><x-icon name="send" class="size-4"/></button>
        </form>
        @error('body')<p class="error">{{ $message }}</p>@enderror
    @else
        <form wire:submit="setNickname" class="flex items-center gap-2">
            <input type="text" wire:model="nickname" maxlength="30" placeholder="{{ __('Как вас зовут?') }}" class="input {{ $compact ? 'py-2.5' : '' }}" autocomplete="nickname" aria-label="{{ __('Ник') }}">
            <button type="submit" class="btn btn-primary shrink-0">{{ __('Войти') }}</button>
        </form>
        @error('nickname')<p class="error">{{ $message }}</p>@enderror
        <p class="mt-2 text-[11px] text-ink-400">{{ __('Или') }} <a href="{{ route('login') }}" class="link">{{ __('войдите в аккаунт') }}</a></p>
    @endif
</div>
