<?php

namespace App\Http\Controllers;

use App\Models\Video;

class VideoController extends Controller
{
    public function show(string $slug)
    {
        $video = Video::published()->where('slug', $slug)->first()
            ?? Video::published()->where('youtube_id', substr($slug, -11))->first()
            ?? Video::published()->where('youtube_id', $slug)->first();
        abort_unless($video, 404);

        if ($video->slug !== $slug) {
            return redirect()->route('videos.show', $video->slug, 301);
        }

        $isShort = $video->type === 'short';
        $related = Video::published()->whereKeyNot($video->id)
            ->when($isShort,
                fn ($q) => $q->where('type', 'short'),
                fn ($q) => $q->where('type', '!=', 'short'))
            ->latest('published_at')->take($isShort ? 12 : 10)->get();

        // Feed order: newest first. "next" = older (scroll down), "prev" = newer (scroll up).
        $date = $video->published_at ?? $video->created_at;
        $next = Video::published()->whereKeyNot($video->id)
            ->where(fn ($q) => $q->where('published_at', '<', $date)
                ->orWhere(fn ($w) => $w->where('published_at', $date)->where('id', '<', $video->id)))
            ->orderByDesc('published_at')->orderByDesc('id')->first();
        $prev = Video::published()->whereKeyNot($video->id)
            ->where(fn ($q) => $q->where('published_at', '>', $date)
                ->orWhere(fn ($w) => $w->where('published_at', $date)->where('id', '>', $video->id)))
            ->orderBy('published_at')->orderBy('id')->first();

        return view('pages.video', compact('video', 'related', 'prev', 'next'));
    }
}
