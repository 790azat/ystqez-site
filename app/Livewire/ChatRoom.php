<?php

namespace App\Livewire;

use App\Models\ChatMessage;
use App\Support\Visitor;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Живой чат')]
class ChatRoom extends Component
{
    #[Locked]
    public string $mode = 'page'; // page | widget

    #[Locked]
    public bool $raised = false; // lift the floating button (e.g. above video feed nav on mobile)

    public bool $open = false;

    public string $nickname = '';

    public string $body = '';

    public string $website = '';

    public function mount(string $mode = 'page', bool $raised = false): void
    {
        $this->raised = $raised;
        $this->mode = $mode === 'widget' ? 'widget' : 'page';
        $this->nickname = (string) (Visitor::nickname() ?? '');
    }

    public function toggle(): void
    {
        $this->open = ! $this->open;
    }

    public function setNickname(): void
    {
        $this->validate(['nickname' => ['required', 'string', 'min:2', 'max:30', 'regex:/^[\pL\pN _.\-]+$/u']], [], ['nickname' => 'ник']);
        Visitor::rememberNickname(trim($this->nickname));
    }

    public function send(): void
    {
        if ($this->website !== '') {
            return;
        }
        $nick = auth()->user()?->name ?? session('guest_nickname');
        if (! $nick) {
            $this->addError('nickname', 'Сначала укажите ник.');

            return;
        }

        $this->validate(['body' => ['required', 'string', 'max:500']], [], ['body' => 'сообщение']);

        $key = 'chat:'.(auth()->id() ?? request()->ip());
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('body', 'Не так быстро! Подождите '.RateLimiter::availableIn($key).' сек.');

            return;
        }
        RateLimiter::hit($key, 15);

        ChatMessage::create([
            'user_id' => auth()->id(),
            'nickname' => mb_substr($nick, 0, 40),
            'body' => trim($this->body),
            'ip' => request()->ip(),
        ]);
        $this->reset('body');
        $this->dispatch('chat-sent');
    }

    public function delete(int $id): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
        ChatMessage::whereKey($id)->delete();
    }

    public function render()
    {
        $limit = $this->mode === 'widget' ? 30 : 80;
        $shouldLoad = $this->mode === 'page' || $this->open;

        return view('livewire.chat-room', [
            'messages' => $shouldLoad ? ChatMessage::latest('id')->take($limit)->get()->reverse()->values() : collect(),
            'hasNick' => (bool) (auth()->user()?->name ?? session('guest_nickname')),
        ]);
    }
}
