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
        return isset(self::TYPES[$this->type]) ? __(self::TYPES[$this->type]) : (string) $this->type;
    }

    public function getStatusLabelAttribute(): string
    {
        return isset(self::STATUSES[$this->status]) ? __(self::STATUSES[$this->status]) : (string) $this->status;
    }
}
