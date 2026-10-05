<?php

namespace App\Livewire\Forum;

use App\Livewire\Concerns\DarkPagination;
use App\Models\ForumPost;
use App\Models\ForumTopic;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class TopicShow extends Component
{
    use DarkPagination;

    public ForumTopic $topic;

    public string $body = '';

    public function mount(ForumTopic $topic): void
    {
        $this->topic = $topic;
        $viewed = session('viewed_topics', []);
        if (! in_array($topic->id, $viewed, true)) {
            $topic->increment('views');
            session(['viewed_topics' => array_slice([...$viewed, $topic->id], -50)]);
        }
    }

    public function reply(): void
    {
        abort_unless(auth()->check(), 403);
        $this->topic->refresh();
        if ($this->topic->is_locked && ! auth()->user()->is_admin) {
            $this->addError('body', __('Тема закрыта для ответов.'));

            return;
        }

        $this->validate(['body' => ['required', 'string', 'min:2', 'max:10000']], [], ['body' => __('ответ')]);

        $key = 'reply:'.auth()->id();
        if (RateLimiter::tooManyAttempts($key, 6)) {
            $this->addError('body', __('Слишком часто. Подождите :seconds сек.', ['seconds' => RateLimiter::availableIn($key)]));

            return;
        }
        RateLimiter::hit($key, 60);

        $this->topic->posts()->create(['user_id' => auth()->id(), 'body' => trim($this->body)]);
        $this->topic->increment('replies_count');
        $this->topic->update(['last_post_at' => now()]);
        $this->reset('body');

        $posts = $this->topic->posts()->count();
        $this->gotoPage((int) ceil($posts / 15));
        $this->dispatch('toast', message: __('Ответ опубликован'));
    }

    public function togglePin(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
        $this->topic->update(['is_pinned' => ! $this->topic->is_pinned]);
    }

    public function toggleLock(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
        $this->topic->update(['is_locked' => ! $this->topic->is_locked]);
    }

    public function deletePost(int $id): void
    {
        $post = ForumPost::where('forum_topic_id', $this->topic->id)->findOrFail($id);
        abort_unless(auth()->user()?->is_admin, 403);
        $first = $this->topic->posts()->oldest('id')->value('id');
        if ($first === $post->id) {
            $category = $this->topic->category;
            $this->topic->delete();
            $this->redirectRoute('forum.category', $category);

            return;
        }
        $post->delete();
        $this->topic->update(['replies_count' => max(0, $this->topic->posts()->count() - 1)]);
    }

    public function render()
    {
        return view('livewire.forum.topic-show', [
            'posts' => $this->topic->posts()->with('user')->oldest('id')->paginate(15),
            'firstPostId' => $this->topic->posts()->oldest('id')->value('id'),
        ])->title($this->topic->title.' — '.__('Форум'));
    }
}
