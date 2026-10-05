<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\AdminOnly;
use App\Services\ContentImporter;
use App\Support\Locale;
use App\Support\Settings as SiteSettings;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Настройки — Админка')]
class Settings extends Component
{
    use AdminOnly;

    public array $values = [];

    /** Keys of the form fields: translatable texts get one field per locale. */
    protected function formKeys(): array
    {
        $keys = [];
        foreach (SiteSettings::FIELDS as $key => $_) {
            if (SiteSettings::isTranslatable($key)) {
                foreach (array_keys(Locale::SUPPORTED) as $loc) {
                    $keys[] = "{$key}_{$loc}";
                }
            } else {
                $keys[] = $key;
            }
        }

        return $keys;
    }

    public function mount(): void
    {
        $all = SiteSettings::all();
        foreach (SiteSettings::FIELDS as $key => $_) {
            if (SiteSettings::isTranslatable($key)) {
                foreach (array_keys(Locale::SUPPORTED) as $loc) {
                    $value = $all["{$key}_{$loc}"] ?? null;
                    if (($value === null || $value === '') && $loc === 'ru') {
                        $value = $all[$key] ?? '';
                    }
                    $this->values["{$key}_{$loc}"] = (string) $value;
                }
            } else {
                $this->values[$key] = (string) ($all[$key] ?? '');
            }
        }
    }

    public function save(): void
    {
        $rules = [];
        foreach ($this->formKeys() as $key) {
            $rules["values.$key"] = ['nullable', 'string', 'max:5000'];
        }
        $this->validate($rules);

        foreach ($this->formKeys() as $key) {
            SiteSettings::set($key, trim((string) ($this->values[$key] ?? '')));
        }
        // Keep the legacy (pre-i18n) key in sync with the Russian text.
        foreach (SiteSettings::TRANSLATABLE as $key) {
            SiteSettings::set($key, trim((string) ($this->values["{$key}_ru"] ?? '')));
        }
        session()->flash('ok', __('Настройки сохранены'));
    }

    public function reimport(): void
    {
        $r = (new ContentImporter)->run();
        $this->mount();
        session()->flash('ok', __('Импорт из database/data: видео — :videos, Instagram — :instagram, канал — :channel', ['videos' => $r['videos'], 'instagram' => $r['instagram'], 'channel' => $r['channel']]));
    }

    public function render()
    {
        return view('livewire.admin.settings', ['fields' => SiteSettings::FIELDS]);
    }
}
