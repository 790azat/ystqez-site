<?php

namespace App\Services;

use App\Models\Video;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class YouTubeService
{
    public const CHANNEL_ID = 'UC9aPP_5pJfGz1OjXppiOCcw';

    /** Fetch oEmbed data (title, thumbnail). Returns null on any failure. */
    public function oembed(string $youtubeId): ?array
    {
        try {
            $response = Http::timeout(6)->acceptJson()->get('https://www.youtube.com/oembed', [
                'url' => "https://www.youtube.com/watch?v={$youtubeId}",
                'format' => 'json',
            ]);
            if (! $response->successful()) {
                return null;
            }
            $data = $response->json();

            return is_array($data) ? [
                'title' => $data['title'] ?? null,
                'thumbnail' => $data['thumbnail_url'] ?? null,
                'author' => $data['author_name'] ?? null,
            ] : null;
        } catch (Throwable $e) {
            Log::warning('YouTube oEmbed failed: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Fetch the channel RSS feed and upsert videos.
     *
     * @return array{created:int, updated:int}
     */
    public function syncRss(?string $channelId = null): array
    {
        $channelId ??= self::CHANNEL_ID;
        $response = Http::timeout(10)->get('https://www.youtube.com/feeds/videos.xml', ['channel_id' => $channelId]);
        if (! $response->successful()) {
            throw new RuntimeException('YouTube вернул статус '.$response->status());
        }

        return $this->importRss($response->body());
    }

    /** @return array{created:int, updated:int} */
    public function importRss(string $xml): array
    {
        $previous = libxml_use_internal_errors(true);
        $feed = simplexml_load_string($xml);
        libxml_use_internal_errors($previous);
        if ($feed === false) {
            throw new RuntimeException('Не удалось разобрать RSS');
        }

        $created = $updated = 0;
        foreach ($feed->entry as $entry) {
            $yt = $entry->children('http://www.youtube.com/xml/schemas/2015');
            $media = $entry->children('http://search.yahoo.com/mrss/');
            $id = (string) $yt->videoId;
            if ($id === '') {
                continue;
            }
            $group = $media->group;
            $description = $group ? (string) $group->description : null;
            $views = null;
            $likes = null;
            $thumb = null;
            if ($group) {
                $community = $group->community;
                if ($community && $community->statistics) {
                    $views = (int) $community->statistics->attributes()->views;
                }
                if ($community && $community->starRating) {
                    $likes = (int) $community->starRating->attributes()->count;
                }
                if ($group->thumbnail) {
                    $thumb = (string) $group->thumbnail->attributes()->url;
                }
            }
            $link = (string) ($entry->link ? $entry->link->attributes()->href : '');

            $video = Video::firstOrNew(['youtube_id' => $id]);
            $isNew = ! $video->exists;
            $video->title = (string) $entry->title ?: ($video->title ?: $id);
            if ($description !== null && $description !== '') {
                $video->description = $description;
            }
            if ((string) $entry->published) {
                $video->published_at = Carbon::parse((string) $entry->published);
            }
            if ($views !== null) {
                $video->view_count = max($views, (int) $video->view_count);
            }
            if ($likes) {
                $video->like_count = $likes;
            }
            if ($isNew) {
                $video->type = str_contains($link, '/shorts/') ? 'short' : 'video';
                $video->thumbnail = $thumb ?: null;
                $video->is_published = true;
            }
            $video->save();
            $isNew ? $created++ : $updated++;
        }

        return ['created' => $created, 'updated' => $updated];
    }
}
