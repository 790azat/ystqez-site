<?php

namespace App\Models;

use App\Support\Ru;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Video extends Model
{
    public const TYPES = [
        'video' => 'Видео',
        'short' => 'Shorts',
        'live' => 'Эфиры',
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'tags' => 'array',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'duration' => 'integer',
            'view_count' => 'integer',
            'like_count' => 'integer',
            'site_likes' => 'integer',
        ];
    }

    /** Keep Cyrillic tags readable in JSON so LIKE-search works. */
    protected function asJson($value, $flags = 0)
    {
        return parent::asJson($value, $flags | JSON_UNESCAPED_UNICODE);
    }

    protected static function booted(): void
    {
        static::saving(function (Video $video) {
            if (! $video->slug || $video->isDirty('title')) {
                $video->slug = static::makeSlug($video->title, $video->youtube_id);
            }
        });
    }

    public static function makeSlug(?string $title, string $youtubeId): string
    {
        $base = Str::slug(Str::limit((string) $title, 70, ''), '-', 'ru');

        return ($base !== '' ? $base.'-' : '').$youtubeId;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function comments(): HasMany
    {
        return $this->hasMany(VideoComment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(VideoLike::class);
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true);
    }

    public function getThumbUrlAttribute(): string
    {
        return $this->thumbnail ?: "https://i.ytimg.com/vi/{$this->youtube_id}/hqdefault.jpg";
    }

    public function getFallbackThumbAttribute(): string
    {
        return "https://i.ytimg.com/vi/{$this->youtube_id}/hqdefault.jpg";
    }

    public function getYoutubeUrlAttribute(): string
    {
        return $this->type === 'short'
            ? "https://www.youtube.com/shorts/{$this->youtube_id}"
            : "https://www.youtube.com/watch?v={$this->youtube_id}";
    }

    public function getDurationHumanAttribute(): ?string
    {
        if (! $this->duration) {
            return null;
        }
        $h = intdiv($this->duration, 3600);
        $m = intdiv($this->duration % 3600, 60);
        $s = $this->duration % 60;

        return $h ? sprintf('%d:%02d:%02d', $h, $m, $s) : sprintf('%d:%02d', $m, $s);
    }

    public function getViewsHumanAttribute(): string
    {
        return Ru::compact($this->view_count).' '.Ru::plural($this->view_count, ['просмотр', 'просмотра', 'просмотров']);
    }

    /** Title without technical hashtags like #shorts. */
    public function getDisplayTitleAttribute(): string
    {
        $clean = trim(preg_replace('~\s*#shorts?\b~iu', '', (string) $this->title));

        return $clean !== '' ? $clean : (string) $this->title;
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? 'Видео';
    }

    /** Escaped description with clickable links and timestamps. */
    public function getDescriptionHtmlAttribute(): string
    {
        $text = e((string) $this->description);

        $text = preg_replace_callback(
            '~(https?://[^\s<]+)~u',
            fn ($m) => '<a href="'.$m[1].'" target="_blank" rel="noopener nofollow" class="link">'.$m[1].'</a>',
            $text
        );

        $text = preg_replace_callback(
            '~(?<![\w:/.])(?:(\d{1,2}):)?(\d{1,2}):(\d{2})(?![\w:])~u',
            function ($m) {
                $seconds = ((int) $m[1]) * 3600 + ((int) $m[2]) * 60 + (int) $m[3];

                return '<button type="button" class="timestamp" data-seek="'.$seconds.'">'.$m[0].'</button>';
            },
            $text
        );

        return nl2br($text);
    }

    /** @return list<array{seconds:int, time:string, label:string}> */
    public function chapters(): array
    {
        $out = [];
        foreach (preg_split('~\R~u', (string) $this->description) as $line) {
            if (preg_match('~^\s*[\-–•]?\s*\(?((?:\d{1,2}:)?\d{1,2}:\d{2})\)?\s*[\-–—:|]?\s*(.+)$~u', $line, $m)) {
                $parts = array_map('intval', explode(':', $m[1]));
                $seconds = count($parts) === 3 ? $parts[0] * 3600 + $parts[1] * 60 + $parts[2] : $parts[0] * 60 + $parts[1];
                $out[] = ['seconds' => $seconds, 'time' => $m[1], 'label' => trim($m[2])];
            }
        }

        return count($out) >= 2 ? $out : [];
    }

    public static function extractYoutubeId(string $input): ?string
    {
        $input = trim($input);
        if (preg_match('~^[A-Za-z0-9_-]{11}$~', $input)) {
            return $input;
        }
        $patterns = [
            '~youtu\.be/([A-Za-z0-9_-]{11})~',
            '~[?&]v=([A-Za-z0-9_-]{11})~',
            '~/(?:shorts|embed|live|v)/([A-Za-z0-9_-]{11})~',
        ];
        foreach ($patterns as $p) {
            if (preg_match($p, $input, $m)) {
                return $m[1];
            }
        }

        return null;
    }
}
