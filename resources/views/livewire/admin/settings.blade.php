<div>
    @include('partials.admin-flash')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-3xl font-semibold">{{ __('Настройки сайта') }}</h1>
        <button wire:click="reimport" wire:confirm="{{ __('Импортировать данные из database/data/*.json?') }}" class="btn btn-ghost"><x-icon name="refresh" class="size-4"/> {{ __('Импорт из JSON') }}</button>
    </div>
    <p class="mt-2 text-sm text-ink-400">{{ __('Изображения указываются внешними ссылками или путями вида') }} <code class="text-ember-300">media/...</code> ({{ __('отдаются через CDN:') }} {{ config('media.cdn_url') ?: __('локально') }}).</p>
    <form wire:submit="save" class="card mt-6 grid gap-5 p-6 md:grid-cols-2">
        @foreach($fields as $key => [$label, $type])
            @if(\App\Support\Settings::isTranslatable($key))
                <fieldset class="md:col-span-2 rounded-2xl bg-white/[.02] p-4 ring-1 ring-white/5">
                    <legend class="label px-1">{{ __($label) }} <span class="font-normal text-ink-400">· {{ __('на трёх языках') }}</span></legend>
                    <div class="grid gap-4 {{ $type === 'textarea' ? '' : 'md:grid-cols-3' }}">
                        @foreach(\App\Support\Locale::SUPPORTED as $loc => $short)
                            @php($field = $key.'_'.$loc)
                            <div>
                                <label class="mb-1 flex items-center gap-2 text-xs font-semibold text-ink-400" for="s-{{ $field }}"><span class="badge bg-white/5 text-ink-300">{{ $short }}</span> {{ \App\Support\Locale::NAMES[$loc] }}</label>
                                @if($type === 'textarea')
                                    <textarea id="s-{{ $field }}" lang="{{ $loc }}" wire:model="values.{{ $field }}" rows="3" class="input" placeholder="{{ \App\Support\Settings::LOCALIZED_DEFAULTS[$key][$loc] ?? '' }}"></textarea>
                                @else
                                    <input id="s-{{ $field }}" lang="{{ $loc }}" type="text" wire:model="values.{{ $field }}" class="input" placeholder="{{ \App\Support\Settings::LOCALIZED_DEFAULTS[$key][$loc] ?? '' }}">
                                @endif
                                @error('values.'.$field)<p class="error">{{ $message }}</p>@enderror
                            </div>
                        @endforeach
                    </div>
                </fieldset>
            @else
            <div @class(['md:col-span-2' => $type === 'textarea'])>
                <label class="label" for="s-{{ $key }}">{{ __($label) }}</label>
                @if($type === 'textarea')
                    <textarea id="s-{{ $key }}" wire:model="values.{{ $key }}" rows="3" class="input" placeholder="{{ \App\Support\Settings::DEFAULTS[$key] ?? '' }}"></textarea>
                @else
                    <input id="s-{{ $key }}" type="text" wire:model="values.{{ $key }}" class="input" placeholder="{{ \App\Support\Settings::DEFAULTS[$key] ?? '' }}">
                @endif
                @error('values.'.$key)<p class="error">{{ $message }}</p>@enderror
            </div>
            @endif
        @endforeach
        <div class="flex justify-end md:col-span-2">
            <button class="btn btn-primary" wire:loading.attr="disabled"><x-icon name="check" class="size-4"/> {{ __('Сохранить') }}</button>
        </div>
    </form>
</div>
