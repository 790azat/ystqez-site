<?php

namespace App\Livewire\Forum;

use App\Livewire\Concerns\DarkPagination;
use App\Models\ForumCategory;
use App\Models\ForumTopic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class CategoryShow extends Component
{
    use DarkPagination;

    public ForumCategory $category;

    public bool $showForm = false;

    public string $title = '';

    public string $body = '';

    public function mount(ForumCategory $category): void
    {
        $this->category = $category;
        $this->showForm = request()->boolean('new') && auth()->check();
    }

    public function openForm(): void
    {
        if (! auth()->check()) {
            $this->redirectRoute('login');

            return;
        }
        $this->showForm = true;
    }

    public function create(): void
    {
        abort_unless(auth()->check(), 403);

        $this->validate([
            'title' => ['required', 'string', 'min:4', 'max:200'],
            'body' => ['required', 'string', 'min:5', 'max:10000'],
        ], [], ['title' => __('заголовок'), 'body' => __('текст')]);

        $key = 'topic:'.auth()->id();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $this->addError('title', __('Слишком много новых тем. Попробуйте через пару минут.'));

            return;
        }
        RateLimiter::hit($key, 300);

        $topic = DB::transaction(function () {
            $topic = ForumTopic::create([
                'forum_category_id' => $this->category->id,
                'user_id' => auth()->id(),
                'title' => trim($this->title),
                'last_post_at' => now(),
            ]);
            $topic->posts()->create(['user_id' => auth()->id(), 'body' => trim($this->body)]);

            return $topic;
        });

        $this->redirectRoute('forum.topic', $topic);
    }

    public function render()
    {
        return view('livewire.forum.category-show', [
            'topics' => ForumTopic::with(['user'])
                ->where('forum_category_id', $this->category->id)
                ->orderByDesc('is_pinned')->orderByDesc('last_post_at')
                ->paginate(20),
        ])->title($this->category->name.' — '.__('Форум'));
    }
}
