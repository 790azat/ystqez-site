<?php

namespace App\Support;

use App\Models\Setting;
use Throwable;

class Settings
{
    /** Editable keys with labels (labels are translation keys, used by admin). */
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

    /** Texts stored per locale as "{key}_{locale}" (e.g. hero_title_hy). */
    public const TRANSLATABLE = ['hero_title', 'hero_subtitle', 'about_text', 'chat_rules'];

    /** Default values for translatable texts, per locale. */
    public const LOCALIZED_DEFAULTS = [
        'hero_title' => [
            'hy' => 'Զրույցներ, որոնցից հետո ուզում ես մտածել',
            'ru' => 'Разговоры, после которых хочется думать',
            'en' => 'Conversations that leave you thinking',
        ],
        'hero_subtitle' => [
            'hy' => 'Yst Qez-ը ընկերների խումբ է, որոնք արդեն 7–8 տարի միասին ճանապարհորդում են և խոսում ամենակարևորի մասին՝ գաղափարների, գրքերի, քաղաքների, մարդկանց ու իմաստների։ Սա սովորական պոդքաստ չէ, այլ ավելի շուտ անկեղծ զրույց խարույկի շուրջ, որին կարող ես միանալ։',
            'ru' => 'Yst Qez — компания друзей, которые уже 7–8 лет путешествуют вместе и разговаривают о главном: идеях, книгах, городах, людях и смыслах. Не подкаст в привычном смысле — скорее, честный разговор у костра, на который можно подсесть.',
            'en' => 'Yst Qez is a group of friends who have been travelling together for 7–8 years, talking about what matters most: ideas, books, cities, people and meaning. Not a podcast in the usual sense — more like an honest campfire conversation you are welcome to join.',
        ],
        'about_text' => [
            'hy' => 'Մենք երկար տարիներ ընկերներ ենք, միասին շրջում ենք աշխարհով և վիճում ամեն ինչի մասին։ Մի օր որոշեցինք միացնել տեսախցիկը, և այդպես ծնվեց Yst Qez-ը։ Այստեղ չկան սցենար և կոստյումավոր փորձագետներ՝ միայն կենդանի զրույց, հետաքրքրասիրություն և հարգանք զրուցակցի հանդեպ։',
            'ru' => 'Мы дружим много лет, вместе ездим по миру и спорим обо всём на свете. Однажды решили включить камеру — так появился Yst Qez. Здесь нет сценария и экспертов в пиджаках: только живая беседа, любопытство и уважение к собеседнику.',
            'en' => 'We have been friends for many years, we travel the world together and argue about everything under the sun. One day we decided to switch on the camera — and that is how Yst Qez was born. No script and no experts in suits: just a live conversation, curiosity and respect for the person you are talking to.',
        ],
        'chat_rules' => [
            'hy' => 'Եղեք քաղաքավարի, առանց սպամի և գովազդի։ Մոդերատորները կարող են ջնջել հաղորդագրությունները։',
            'ru' => 'Будьте вежливы, без спама и рекламы. Модераторы могут удалять сообщения.',
            'en' => 'Be polite, no spam or advertising. Moderators may delete messages.',
        ],
    ];

    /** Defaults for non-translatable keys (translatable ones: Russian default kept for compatibility). */
    public const DEFAULTS = [
        'hero_title' => self::LOCALIZED_DEFAULTS['hero_title']['ru'],
        'hero_subtitle' => self::LOCALIZED_DEFAULTS['hero_subtitle']['ru'],
        'about_text' => self::LOCALIZED_DEFAULTS['about_text']['ru'],
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
        'chat_rules' => self::LOCALIZED_DEFAULTS['chat_rules']['ru'],
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

    public static function isTranslatable(string $key): bool
    {
        return in_array($key, static::TRANSLATABLE, true);
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        if (static::isTranslatable($key)) {
            return static::localized($key, Locale::current()) ?? $default;
        }

        $value = static::all()[$key] ?? null;
        if ($value === null || $value === '') {
            return $default ?? (static::DEFAULTS[$key] ?? null);
        }

        return $value;
    }

    /**
     * Value of a translatable text for a locale.
     * Order: {key}_{locale} → Russian ({key}_ru, legacy {key}) → any other locale → built-in default.
     */
    public static function localized(string $key, string $locale): ?string
    {
        $all = static::all();
        $candidates = ["{$key}_{$locale}", "{$key}_ru", $key];
        foreach (array_keys(Locale::SUPPORTED) as $loc) {
            $candidates[] = "{$key}_{$loc}";
        }
        foreach ($candidates as $candidate) {
            $value = $all[$candidate] ?? null;
            if ($value !== null && trim($value) !== '') {
                return $value;
            }
        }

        return static::LOCALIZED_DEFAULTS[$key][$locale]
            ?? static::LOCALIZED_DEFAULTS[$key]['ru']
            ?? static::DEFAULTS[$key]
            ?? null;
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
