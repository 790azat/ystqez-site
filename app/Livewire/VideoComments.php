<?php

namespace App\Livewire;

use App\Models\VideoComment;
use App\Support\Visitor;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class VideoComments extends Component
{
    public int $videoId;

    public string $name = '';

    public string $body = '';

    public string $website = ''; // honeypot

    public int $limit = 20;

    public function mount(int $videoId): void
    {
        $this->videoId = $videoId;
        $this->name = (string) (session('guest_nickname') ?? '');
    }

    protected function rules(): array
    {
        return [
            'name' => auth()->check() ? ['nullable'] : ['required', 'string', 'min:2', 'max:40'],
            'body' => ['required', 'string', 'min:2', 'max:2000'],
        ];
    }

    protected function validationAttributes(): array
    {
        return ['name' => __('имя'), 'body' => __('комментарий')];
    }

    public function post(): void
    {
        if ($this->website !== '') {
            $this->reset('body');

            return;
        }
        $this->validate();

        $key = 'comment:'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $this->addError('body', __('Слишком часто. Попробуйте через :seconds сек.', ['seconds' => RateLimiter::availableIn($key)]));

            return;
        }
        RateLimiter::hit($key, 60);

        if (! auth()->check()) {
            Visitor::rememberNickname(trim($this->name));
        }

        VideoComment::create([
            'video_id' => $this->videoId,
            'user_id' => auth()->id(),
            'guest_name' => auth()->check() ? null : trim($this->name),
            'body' => trim($this->body),
            'ip' => request()->ip(),
        ]);

        $this->reset('body');
        $this->dispatch('toast', message: __('Комментарий опубликован'));
    }

    public function delete(int $id): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
        VideoComment::whereKey($id)->where('video_id', $this->videoId)->delete();
    }

    public function more(): void
    {
        $this->limit += 20;
    }

    public function render()
    {
        $query = VideoComment::with('user')->where('video_id', $this->videoId);

        return view('livewire.video-comments', [
            'total' => (clone $query)->count(),
            'comments' => $query->latest()->take($this->limit)->get(),
        ]);
    }
}
