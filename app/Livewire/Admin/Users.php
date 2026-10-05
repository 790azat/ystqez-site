<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\DarkPagination;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use App\Livewire\Concerns\AdminOnly;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Пользователи — Админка')]
class Users extends Component
{
    use AdminOnly, DarkPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function toggleAdmin(int $id): void
    {
        if ($id === auth()->id()) {
            session()->flash('error', __('Нельзя снять права администратора с самого себя.'));

            return;
        }
        $u = User::findOrFail($id);
        $u->update(['is_admin' => ! $u->is_admin]);
    }

    public function delete(int $id): void
    {
        if ($id === auth()->id()) {
            return;
        }
        User::whereKey($id)->delete();
        session()->flash('ok', __('Пользователь удалён'));
    }

    public function render()
    {
        return view('livewire.admin.users', [
            'users' => User::withCount(['topics', 'posts'])
                ->when($this->search !== '', fn ($q) => $q->where(fn ($w) => $w
                    ->whereRaw('LOWER(name) LIKE ?', ['%'.mb_strtolower($this->search).'%'])
                    ->orWhereRaw('LOWER(email) LIKE ?', ['%'.mb_strtolower($this->search).'%'])))
                ->latest()->paginate(30),
        ]);
    }
}
