<?php

namespace App\Support;

use App\Models\Setting;
use Throwable;

class Settings
{
    /** Editable keys with labels (used by admin). */
    public const FIELDS = [
        'hero_title' => ['Заголовок на главной', 'text'],
        'hero_subtitle' => ['Подзаголовок на главной', 'textarea'],
        'about_text' => ['О проекте (блок «Кто мы»)', 'textarea'],
        'youtube_url' => ['Ссылка на YouTube', 'text'],
        'instagram_url' => ['Ссылка на Instagram', 'text'],
        'telegram_url' => ['Ссылка на Telegram', 'text'],
        'contact_email' => ['Email для связи', 'text'],
        'contact_telegram' => ['Telegram для связи (@ник)', 'text'],
        'channel_title' => ['Название канала', 'text'],
        'channel_description' => ['Описание канала', 'textarea'],
        'channel_avatar' => ['URL аватара канала', 'text'],
        'channel_banner' => ['URL баннера канала', 'text'],
        'channel_subscribers' => ['Число подписчиков', 'text'],
        'chat_rules' => ['Правила чата', 'textarea'],
    ];

    public const DEFAULTS = [
        'hero_title' => 'Разговоры, после которых хочется думать',
        'hero_subtitle' => 'Yst Qez — компания друзей, которые уже 7–8 лет путешествуют вместе и разговаривают о главном: идеях, книгах, городах, людях и смыслах. Не подкаст в привычном смысле — скорее, честный разговор у костра, на который можно подсесть.',
        'about_text' => 'Мы дружим много лет, вместе ездим по миру и спорим обо всём на свете. Однажды решили включить камеру — так появился Yst Qez. Здесь нет сценария и экспертов в пиджаках: только живая беседа, любопытство и уважение к собеседнику.',
        'youtube_url' => 'https://www.youtube.com/@YstQez',
        'instagram_url' => 'https://www.instagram.com/yst.qez/',
        'telegram_url' => '',
        'contact_email' => '',
        'contact_telegram' => '',
        'channel_title' => 'Yst Qez',
        'channel_description' => '',
        'channel_avatar' => '',
        'channel_banner' => '',
        'channel_subscribers' => '',
        'chat_rules' => 'Будьте вежливы, без спама и рекламы. Модераторы могут удалять сообщения.',
    ];

    protected static ?array $cache = null;

    public static function all(): array
    {
        if (static::$cache === null) {
            try {
                static::$cache = Setting::query()->pluck('value', 'key')->all();
            } catch (Throwable) {
                static::$cache = [];
            }
        }

        return static::$cache;
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $value = static::all()[$key] ?? null;
        if ($value === null || $value === '') {
            return $default ?? (static::DEFAULTS[$key] ?? null);
        }

        return $value;
    }

    public static function set(string $key, ?string $value): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        static::flush();
    }

    public static function flush(): void
    {
        static::$cache = null;
    }
}
