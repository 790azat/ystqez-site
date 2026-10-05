<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\DarkPagination;
use App\Models\VideoComment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Livewire\Concerns\AdminOnly;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Комментарии — Админка')]
class Comments extends Component
{
    use AdminOnly, DarkPagination;

    public function delete(int $id): void
    {
        VideoComment::whereKey($id)->delete();
    }

    public function render()
    {
        return view('livewire.admin.comments', [
            'comments' => VideoComment::with(['video', 'user'])->latest()->paginate(30),
        ]);
    }
}
