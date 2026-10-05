<?php

namespace App\Models;

use App\Support\Locale;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForumCategory extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'name_translations' => 'array',
            'description_translations' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function topics(): HasMany
    {
        return $this->hasMany(ForumTopic::class);
    }

    /** Name in the current locale (fallback: Russian → stored name → any translation). */
    protected function name(): Attribute
    {
        return Attribute::get(fn (?string $value) => $this->translated('name_translations', $value));
    }

    protected function description(): Attribute
    {
        return Attribute::get(fn (?string $value) => $this->translated('description_translations', $value));
    }

    protected function translated(string $column, ?string $raw): ?string
    {
        $translations = $this->{$column};
        if (! is_array($translations) || $translations === []) {
            return $raw;
        }
        foreach ([Locale::current(), 'ru'] as $loc) {
            if (filled($translations[$loc] ?? null)) {
                return $translations[$loc];
            }
        }

        return filled($raw) ? $raw : (collect($translations)->first(fn ($v) => filled($v)) ?? $raw);
    }
}
