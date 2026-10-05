<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollabRequest extends Model
{
    public const TYPES = [
        'ads' => 'Реклама',
        'integration' => 'Интеграция',
        'guest' => 'Гость в выпуск',
        'other' => 'Другое',
    ];

    public const STATUSES = [
        'new' => 'Новая',
        'in_progress' => 'В работе',
        'done' => 'Завершена',
    ];

    protected $guarded = ['id'];

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
