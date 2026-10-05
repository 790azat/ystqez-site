<?php

namespace App\Livewire\Forum;

use App\Models\ForumCategory;
use App\Models\ForumPost;
use App\Models\ForumTopic;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Форум')]
class Index extends Component
{
    public function render()
    {
        $categories = ForumCategory::withCount('topics')
            ->orderBy('sort')->orderBy('name')->get()
            ->each(function ($c) {
                $c->latest_topic = ForumTopic::where('forum_category_id', $c->id)
                    ->with('user')->latest('last_post_at')->first();
                $c->posts_count = ForumPost::whereIn('forum_topic_id', ForumTopic::select('id')->where('forum_category_id', $c->id))->count();
            });

        return view('livewire.forum.index', [
            'categories' => $categories,
            'recent' => ForumTopic::with(['category', 'user'])->latest('last_post_at')->take(8)->get(),
            'stats' => [
                'topics' => ForumTopic::count(),
                'posts' => ForumPost::count(),
                'users' => User::count(),
            ],
        ]);
    }
}
