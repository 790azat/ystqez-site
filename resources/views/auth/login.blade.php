<x-layouts.app title="{{ __('Вход') }}">
<div class="container-yq grid min-h-[70vh] place-items-center py-12">
    <div class="relative w-full max-w-md">
        <div class="absolute -inset-6 -z-10 rounded-[3rem] bg-gradient-to-br from-ember-500/20 to-sun-500/5 blur-3xl"></div>
        <form method="POST" action="{{ route('login') }}" class="card space-y-5 p-8 sm:p-10">
            @csrf
            <div class="text-center">
                <h1 class="text-3xl font-semibold">{{ __('С возвращением') }}</h1>
                <p class="mt-2 text-sm text-ink-400">{{ __('Войдите, чтобы писать на форуме и в чате под своим именем.') }}</p>
            </div>
            <div>
                <label class="label" for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="input">
                @error('email')<p class="error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label" for="password">{{ __('Пароль') }}</label>
                <input id="password" type="password" name="password" required autocomplete="current-password" class="input">
                @error('password')<p class="error">{{ $message }}</p>@enderror
            </div>
            <label class="flex items-center gap-2 text-sm text-ink-300">
                <input type="checkbox" name="remember" value="1" class="size-4 rounded accent-ember-500"> {{ __('Запомнить меня') }}
            </label>
            <button class="btn btn-primary btn-lg w-full">{{ __('Войти') }}</button>
            <p class="text-center text-sm text-ink-400">{{ __('Нет аккаунта?') }} <a href="{{ route('register') }}" class="link">{{ __('Зарегистрироваться') }}</a></p>
        </form>
    </div>
</div>
</x-layouts.app>
