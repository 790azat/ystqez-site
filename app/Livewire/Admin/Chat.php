<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\DarkPagination;
use App\Models\ChatMessage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Livewire\Concerns\AdminOnly;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Чат — Админка')]
class Chat extends Component
{
    use AdminOnly, DarkPagination;

    public function delete(int $id): void
    {
        ChatMessage::whereKey($id)->delete();
    }

    public function purgeOld(): void
    {
        $n = ChatMessage::where('created_at', '<', now()->subDays(30))->delete();
        session()->flash('ok', "Удалено сообщений старше 30 дней: {$n}");
    }

    public function render()
    {
        return view('livewire.admin.chat', [
            'messages' => ChatMessage::with('user')->latest('id')->paginate(50),
        ]);
    }
}
