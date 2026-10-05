<div>
    @include('partials.admin-flash')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-3xl font-semibold">Настройки сайта</h1>
        <button wire:click="reimport" wire:confirm="Импортировать данные из database/data/*.json?" class="btn btn-ghost"><x-icon name="refresh" class="size-4"/> Импорт из JSON</button>
    </div>
    <p class="mt-2 text-sm text-ink-400">Изображения указываются внешними ссылками или путями вида <code class="text-ember-300">media/...</code> (отдаются через CDN: {{ config('media.cdn_url') ?: 'локально' }}).</p>
    <form wire:submit="save" class="card mt-6 grid gap-5 p-6 md:grid-cols-2">
        @foreach($fields as $key => [$label, $type])
            <div @class(['md:col-span-2' => $type === 'textarea'])>
                <label class="label" for="s-{{ $key }}">{{ $label }}</label>
                @if($type === 'textarea')
                    <textarea id="s-{{ $key }}" wire:model="values.{{ $key }}" rows="3" class="input" placeholder="{{ \App\Support\Settings::DEFAULTS[$key] ?? '' }}"></textarea>
                @else
                    <input id="s-{{ $key }}" type="text" wire:model="values.{{ $key }}" class="input" placeholder="{{ \App\Support\Settings::DEFAULTS[$key] ?? '' }}">
                @endif
                @error('values.'.$key)<p class="error">{{ $message }}</p>@enderror
            </div>
        @endforeach
        <div class="flex justify-end md:col-span-2">
            <button class="btn btn-primary" wire:loading.attr="disabled"><x-icon name="check" class="size-4"/> Сохранить</button>
        </div>
    </form>
</div>
