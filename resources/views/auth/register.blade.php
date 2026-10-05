<x-layouts.app title="Регистрация">
<div class="container-yq grid min-h-[70vh] place-items-center py-12">
    <div class="relative w-full max-w-md">
        <div class="absolute -inset-6 -z-10 rounded-[3rem] bg-gradient-to-br from-ember-500/20 to-sun-500/5 blur-3xl"></div>
        <form method="POST" action="{{ route('register') }}" class="card space-y-5 p-8 sm:p-10">
            @csrf
            <div class="text-center">
                <h1 class="text-3xl font-semibold">Присоединяйтесь</h1>
                <p class="mt-2 text-sm text-ink-400">Аккаунт нужен для форума — читать и смотреть можно и так.</p>
            </div>
            <div>
                <label class="label" for="name">Имя на сайте</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required maxlength="40" autocomplete="nickname" class="input">
                @error('name')<p class="error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label" for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="input">
                @error('email')<p class="error">{{ $message }}</p>@enderror
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="label" for="password">Пароль</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password" class="input">
                </div>
                <div>
                    <label class="label" for="password_confirmation">Ещё раз</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="input">
                </div>
            </div>
            @error('password')<p class="error -mt-3">{{ $message }}</p>@enderror
            <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true">
            <button class="btn btn-primary btn-lg w-full">Создать аккаунт</button>
            <p class="text-center text-sm text-ink-400">Уже с нами? <a href="{{ route('login') }}" class="link">Войти</a></p>
        </form>
    </div>
</div>
</x-layouts.app>
