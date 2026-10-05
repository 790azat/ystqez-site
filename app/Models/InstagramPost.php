<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstagramPost extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'media_urls' => 'array',
            'posted_at' => 'datetime',
        ];
    }

    public function getImageUrlAttribute(): ?string
    {
        return media_url($this->local_image) ?? ($this->media_urls[0] ?? null);
    }

    public function getUrlAttribute(): string
    {
        return "https://www.instagram.com/p/{$this->shortcode}/";
    }

    public function getIsVideoAttribute(): bool
    {
        return in_array(strtolower((string) $this->type), ['video', 'reel', 'clips', 'graphvideo'], true);
    }
}
