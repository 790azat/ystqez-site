<?php

namespace App\Livewire;

use App\Models\Video;
use App\Models\VideoLike;
use App\Support\Visitor;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class VideoLikes extends Component
{
    public int $videoId;

    public int $count = 0;

    public bool $liked = false;

    public function mount(int $videoId): void
    {
        $this->videoId = $videoId;
        $this->count = (int) Video::whereKey($videoId)->value('site_likes');
        $this->liked = VideoLike::where('video_id', $videoId)->where('visitor_key', Visitor::key())->exists();
    }

    public function toggle(): void
    {
        $key = 'like:'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 20)) {
            return;
        }
        RateLimiter::hit($key, 60);

        $visitor = Visitor::key();
        $existing = VideoLike::where('video_id', $this->videoId)->where('visitor_key', $visitor)->first();
        if ($existing) {
            $existing->delete();
            Video::whereKey($this->videoId)->where('site_likes', '>', 0)->decrement('site_likes');
            $this->liked = false;
        } else {
            VideoLike::create(['video_id' => $this->videoId, 'visitor_key' => $visitor, 'user_id' => auth()->id()]);
            Video::whereKey($this->videoId)->increment('site_likes');
            $this->liked = true;
        }
        $this->count = (int) Video::whereKey($this->videoId)->value('site_likes');
    }

    public function render()
    {
        return view('livewire.video-likes');
    }
}
