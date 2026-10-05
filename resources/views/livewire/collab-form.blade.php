<div class="container-yq pt-10">
    <div class="grid gap-10 lg:grid-cols-[1fr_1.1fr] lg:gap-16">
        <div>
            <p class="kicker"><span class="h-px w-6 bg-ember-500"></span>Сотрудничество</p>
            <h1 class="mt-2 text-4xl font-semibold leading-tight sm:text-5xl">Давайте сделаем <span class="gradient-text">что-то хорошее</span> вместе</h1>
            <p class="mt-5 text-lg leading-relaxed text-ink-300">Мы работаем с брендами и людьми, которые нам по-настоящему близки. Наша аудитория — думающие, любопытные люди, которые смотрят выпуски до конца.</p>

            <div class="mt-10 grid gap-3 sm:grid-cols-2">
                @foreach([
                    ['zap', 'Реклама', 'Рекламная вставка в выпуске или Shorts — коротко и честно.'],
                    ['sparkles', 'Интеграция', 'Нативная история о продукте внутри разговора.'],
                    ['mic', 'Гость в выпуск', 'Есть что сказать? Приходите поговорить с нами.'],
                    ['compass', 'Другое', 'Совместные поездки, проекты, мероприятия — удивите нас.'],
                ] as [$icon, $h, $t])
                    <div class="card p-5">
                        <span class="grid size-10 place-items-center rounded-xl bg-ember-500/15 text-ember-300"><x-icon :name="$icon" class="size-5"/></span>
                        <h3 class="mt-4 font-sans font-bold">{{ $h }}</h3>
                        <p class="mt-1 text-sm leading-relaxed text-ink-400">{{ $t }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 flex flex-wrap gap-x-8 gap-y-3 text-sm text-ink-400">
                @if(setting('contact_email'))<a href="mailto:{{ setting('contact_email') }}" class="inline-flex items-center gap-2 hover:text-white"><x-icon name="mail" class="size-4"/> {{ setting('contact_email') }}</a>@endif
                @if(setting('contact_telegram'))<span class="inline-flex items-center gap-2"><x-icon name="telegram" class="size-4"/> {{ setting('contact_telegram') }}</span>@endif
                <span class="inline-flex items-center gap-2"><x-icon name="clock" class="size-4"/> Отвечаем в течение 2–3 дней</span>
            </div>
        </div>

        <div class="relative">
            <div class="absolute -inset-4 -z-10 rounded-[2.5rem] bg-gradient-to-br from-ember-500/15 to-sun-500/5 blur-2xl"></div>
            @if($sent)
                <div class="card flex min-h-[32rem] flex-col items-center justify-center p-10 text-center animate-fade-up">
                    <span class="grid size-20 place-items-center rounded-full bg-gradient-to-br from-ember-500 to-sun-500 text-ink-950 shadow-2xl shadow-ember-500/40">
                        <x-icon name="check" class="size-10"/>
                    </span>
                    <h2 class="mt-8 text-3xl font-semibold">Заявка отправлена!</h2>
                    <p class="mt-3 max-w-sm text-ink-300">Спасибо, что написали. Мы внимательно прочитаем и свяжемся с вами по указанному контакту.</p>
                    <div class="mt-8 flex flex-wrap justify-center gap-3">
                        <a href="{{ route('videos.index') }}" class="btn btn-primary">Смотреть выпуски</a>
                        <button wire:click="again" class="btn btn-ghost">Отправить ещё одну</button>
                    </div>
                </div>
            @else
                <form wire:submit="submit" class="card space-y-5 p-6 sm:p-8" novalidate>
                    <div>
                        <span class="label">Формат</span>
                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                            @foreach(\App\Models\CollabRequest::TYPES as $key => $label)
                                <label @class(['cursor-pointer rounded-2xl px-3 py-3 text-center text-sm font-bold ring-1 transition', 'bg-ember-500/15 text-ember-300 ring-ember-500/50' => $type === $key, 'bg-white/[.03] text-ink-300 ring-white/10 hover:ring-white/20' => $type !== $key])>
                                    <input type="radio" wire:model.live="type" value="{{ $key }}" class="sr-only">{{ $label }}
                                </label>
                            @endforeach
                        </div>
                        @error('type')<p class="error">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="label" for="cf-name">Имя *</label>
                            <input id="cf-name" type="text" wire:model.blur="name" class="input" placeholder="Как к вам обращаться" autocomplete="name">
                            @error('name')<p class="error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="label" for="cf-contact">Email или Telegram *</label>
                            <input id="cf-contact" type="text" wire:model.blur="contact" class="input" placeholder="you@mail.ru или @username">
                            @error('contact')<p class="error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="label" for="cf-company">Компания / проект</label>
                            <input id="cf-company" type="text" wire:model.blur="company" class="input" placeholder="Необязательно" autocomplete="organization">
                            @error('company')<p class="error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="label" for="cf-budget">Бюджет</label>
                            <select id="cf-budget" wire:model="budget" class="input">
                                <option value="">Не определён</option>
                                <option>до 30 000 ₽</option>
                                <option>30 000 – 100 000 ₽</option>
                                <option>100 000 – 300 000 ₽</option>
                                <option>более 300 000 ₽</option>
                                <option>Бартер / без бюджета</option>
                            </select>
                            @error('budget')<p class="error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="label" for="cf-message">Расскажите об идее *</label>
                        <textarea id="cf-message" wire:model.blur="message" rows="6" class="input" placeholder="Что вы хотите предложить, сроки, ссылки…"></textarea>
                        @error('message')<p class="error">{{ $message }}</p>@enderror
                    </div>
                    <input type="text" wire:model="website" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true">
                    <button type="submit" class="btn btn-primary btn-lg w-full" wire:loading.attr="disabled" wire:target="submit">
                        <span wire:loading.remove wire:target="submit" class="inline-flex items-center gap-2"><x-icon name="send" class="size-5"/> Отправить заявку</span>
                        <span wire:loading wire:target="submit">Отправляем…</span>
                    </button>
                    <p class="text-center text-xs text-ink-400">Нажимая кнопку, вы соглашаетесь на обработку указанных контактных данных для ответа на заявку.</p>
                </form>
            @endif
        </div>
    </div>
</div>
