<?php

namespace App\Services;

use App\Models\InstagramPost;
use App\Models\Video;
use App\Support\Settings;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Throwable;

/**
 * Imports pre-scraped content from database/data/*.json (all files optional).
 */
class ContentImporter
{
    public function __construct(protected ?string $dir = null)
    {
        $this->dir ??= database_path('data');
    }

    /** @return array<string,int|string> */
    public function run(): array
    {
        return [
            'videos' => $this->importVideos(),
            'channel' => $this->importChannel() ? 'ok' : 'skip',
            'instagram' => $this->importInstagram(),
        ];
    }

    protected function readJson(string $file): mixed
    {
        $path = $this->dir.DIRECTORY_SEPARATOR.$file;
        if (! is_file($path)) {
            return null;
        }
        try {
            return json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            return null;
        }
    }

    protected function date(mixed $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }
        try {
            if (is_numeric($value) && strlen((string) $value) === 8) {
                return Carbon::createFromFormat('Ymd', (string) $value)->startOfDay();
            }
            if (is_numeric($value)) {
                return Carbon::createFromTimestamp((int) $value);
            }

            return Carbon::parse((string) $value);
        } catch (Throwable) {
            return null;
        }
    }

    public function importVideos(): int
    {
        $items = $this->readJson('videos.json');
        if (! is_array($items)) {
            return 0;
        }
        if (isset($items['entries']) && is_array($items['entries'])) {
            $items = $items['entries'];
        }

        $count = 0;
        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }
            $id = $item['id'] ?? $item['youtube_id'] ?? null;
            if (! is_string($id) || $id === '') {
                continue;
            }
            $type = strtolower((string) ($item['type'] ?? 'video'));
            if (! array_key_exists($type, Video::TYPES)) {
                $type = match (true) {
                    str_contains($type, 'short') => 'short',
                    str_contains($type, 'live') || str_contains($type, 'stream') => 'live',
                    default => 'video',
                };
            }
            $tags = $item['tags'] ?? [];
            $tags = is_array($tags) ? array_values(array_filter(array_map('strval', $tags))) : [];

            $video = Video::firstOrNew(['youtube_id' => $id]);
            $video->fill(array_filter([
                'title' => isset($item['title']) ? mb_substr((string) $item['title'], 0, 250) : null,
                'description' => $item['description'] ?? null,
                'published_at' => $this->date($item['upload_date'] ?? $item['published_at'] ?? $item['timestamp'] ?? null),
                'duration' => isset($item['duration']) && is_numeric($item['duration']) ? (int) $item['duration'] : null,
                'view_count' => isset($item['view_count']) && is_numeric($item['view_count']) ? (int) $item['view_count'] : null,
                'like_count' => isset($item['like_count']) && is_numeric($item['like_count']) ? (int) $item['like_count'] : null,
                'thumbnail' => $this->pickThumbnail($item),
                'tags' => $tags ?: null,
            ], fn ($v) => $v !== null));
            $video->type = $type;
            if (! $video->title) {
                $video->title = $id;
            }
            if (! $video->exists) {
                $video->is_published = true;
            }
            $video->save();
            $count++;
        }

        return $count;
    }

    protected function pickThumbnail(array $item): ?string
    {
        $thumb = $item['thumbnail'] ?? null;
        if (is_string($thumb) && $thumb !== '') {
            return $thumb;
        }
        $thumbs = $item['thumbnails'] ?? null;
        if (is_array($thumbs) && $thumbs) {
            $last = end($thumbs);

            return is_array($last) ? ($last['url'] ?? null) : null;
        }

        return null;
    }

    public function importChannel(): bool
    {
        $data = $this->readJson('channel.json');
        if (! is_array($data)) {
            return false;
        }
        $map = [
            'channel_title' => $data['title'] ?? $data['channel'] ?? null,
            'channel_description' => $data['description'] ?? null,
            'channel_avatar' => $data['avatar'] ?? null,
            'channel_banner' => $data['banner'] ?? null,
            'channel_subscribers' => isset($data['subscriber_count']) ? (string) $data['subscriber_count'] : null,
        ];
        foreach ($map as $key => $value) {
            if (is_string($value) && $value !== '') {
                Settings::set($key, $value);
            }
        }

        return true;
    }

    public function importInstagram(): int
    {
        $data = $this->readJson('instagram.json');
        if (! is_array($data)) {
            return 0;
        }
        $posts = $data['posts'] ?? (array_is_list($data) ? $data : []);

        $profile = $data['profile'] ?? null;
        if (is_array($profile)) {
            $followers = $profile['followers'] ?? $profile['follower_count'] ?? $profile['edge_followed_by']['count'] ?? null;
            if ($followers !== null) {
                Settings::set('instagram_followers', (string) $followers);
            }
            if (! empty($profile['biography'] ?? $profile['bio'] ?? null)) {
                Settings::set('instagram_bio', (string) ($profile['biography'] ?? $profile['bio']));
            }
            $pic = $profile['local_avatar'] ?? $profile['profile_pic_url'] ?? $profile['avatar'] ?? null;
            if (is_string($pic) && $pic !== '') {
                Settings::set('instagram_avatar', $pic);
            }
        }

        $count = 0;
        foreach ((array) $posts as $post) {
            if (! is_array($post) || empty($post['shortcode'])) {
                continue;
            }
            $media = $post['media_urls'] ?? [];
            if (is_string($media)) {
                $media = [$media];
            }
            InstagramPost::updateOrCreate(
                ['shortcode' => (string) $post['shortcode']],
                [
                    'caption' => $post['caption'] ?? null,
                    'posted_at' => $this->date($post['date'] ?? $post['timestamp'] ?? null),
                    'type' => isset($post['type']) ? (string) $post['type'] : null,
                    'media_urls' => array_values(array_filter(Arr::wrap($media), 'is_string')),
                    'local_image' => $post['local_image'] ?? null,
                    'likes' => is_numeric($post['likes'] ?? null) ? (int) $post['likes'] : 0,
                ]
            );
            $count++;
        }

        return $count;
    }
}
