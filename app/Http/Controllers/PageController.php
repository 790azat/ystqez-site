<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ForumTopic;
use App\Models\InstagramPost;
use App\Models\Video;

class PageController extends Controller
{
    public function home()
    {
        $featured = Video::published()->where('is_featured', true)->latest('published_at')->first();
        $recent = Video::published()
            ->when($featured, fn ($q) => $q->whereKeyNot($featured->id))
            ->latest('published_at')->latest('id')->take(13)->get();
        $featured ??= $recent->shift();
        $recent = $recent->take(12);

        // Hero showcase: featured + two more recent shorts (fan of vertical tiles)
        $showcase = $featured && $featured->type === 'short'
            ? collect([$featured])->merge($recent->where('type', 'short')->take(2))->values()
            : collect();

        $longs = $recent->where('type', '!=', 'short')->take(6)->values();
        $shorts = $recent->where('type', 'short')->take(12)->values();

        $topics = ForumTopic::with(['category', 'user'])
            ->orderByDesc('replies_count')->orderByDesc('last_post_at')->take(5)->get();
        $instagram = InstagramPost::latest('posted_at')->take(8)->get();
        $chat = ChatMessage::latest('id')->take(4)->get()->reverse();
        $stats = [
            'videos' => Video::published()->count(),
            'views' => (int) Video::published()->sum('view_count'),
            'instagram_posts' => InstagramPost::count(),
        ];

        return view('pages.home', compact('featured', 'showcase', 'longs', 'shorts', 'topics', 'instagram', 'chat', 'stats'));
    }

    public function instagram()
    {
        $posts = InstagramPost::latest('posted_at')->paginate(24);

        return view('pages.instagram', compact('posts'));
    }
}
