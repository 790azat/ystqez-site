<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\DarkPagination;
use App\Models\CollabRequest;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use App\Livewire\Concerns\AdminOnly;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Заявки — Админка')]
class Collabs extends Component
{
    use AdminOnly, DarkPagination;

    #[Url(except: '')]
    public string $status = '';

    public ?int $openId = null;

    public string $note = '';

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function open(int $id): void
    {
        $this->openId = $this->openId === $id ? null : $id;
        $this->note = (string) CollabRequest::whereKey($id)->value('admin_note');
    }

    public function setStatus(int $id, string $status): void
    {
        validator(['s' => $status], ['s' => Rule::in(array_keys(CollabRequest::STATUSES))])->validate();
        CollabRequest::whereKey($id)->update(['status' => $status]);
    }

    public function saveNote(int $id): void
    {
        $this->validate(['note' => ['nullable', 'string', 'max:5000']]);
        CollabRequest::whereKey($id)->update(['admin_note' => $this->note]);
        session()->flash('ok', 'Заметка сохранена');
    }

    public function delete(int $id): void
    {
        CollabRequest::whereKey($id)->delete();
        $this->openId = null;
    }

    public function render()
    {
        return view('livewire.admin.collabs', [
            'items' => CollabRequest::query()
                ->when($this->status !== '', fn ($q) => $q->where('status', $this->status))
                ->latest()->paginate(20),
            'counts' => CollabRequest::selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status'),
        ]);
    }
}
