<?php

namespace App\Livewire\Admin;

use App\Services\ContentImporter;
use App\Support\Settings as SiteSettings;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Livewire\Concerns\AdminOnly;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Настройки — Админка')]
class Settings extends Component
{
    use AdminOnly;
    public array $values = [];

    public function mount(): void
    {
        foreach (SiteSettings::FIELDS as $key => $_) {
            $this->values[$key] = (string) (SiteSettings::all()[$key] ?? '');
        }
    }

    public function save(): void
    {
        $rules = [];
        foreach (SiteSettings::FIELDS as $key => $_) {
            $rules["values.$key"] = ['nullable', 'string', 'max:5000'];
        }
        $this->validate($rules);

        foreach (SiteSettings::FIELDS as $key => $_) {
            SiteSettings::set($key, trim((string) ($this->values[$key] ?? '')));
        }
        session()->flash('ok', 'Настройки сохранены');
    }

    public function reimport(): void
    {
        $r = (new ContentImporter)->run();
        $this->mount();
        session()->flash('ok', 'Импорт из database/data: видео — '.$r['videos'].', Instagram — '.$r['instagram'].', канал — '.$r['channel']);
    }

    public function render()
    {
        return view('livewire.admin.settings', ['fields' => SiteSettings::FIELDS]);
    }
}
