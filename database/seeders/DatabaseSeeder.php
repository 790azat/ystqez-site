<?php

namespace Database\Seeders;

use App\Models\ForumCategory;
use App\Models\Setting;
use App\Models\User;
use App\Services\ContentImporter;
use App\Support\Settings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Idempotent: safe to run many times (used by /_setup/{token}).
 */
class DatabaseSeeder extends Seeder
{
    /** Default forum categories: emoji, translations of name and description. */
    public const CATEGORIES = [
        [
            'emoji' => '💬',
            'name' => [
                'hy' => 'Ընդհանուր զրույց',
                'ru' => 'Общий разговор',
                'en' => 'General chat',
            ],
            'description' => [
                'hy' => 'Ամեն ինչի մասին՝ ծանոթություններ, մտքեր, հարցեր։',
                'ru' => 'Обо всём на свете — знакомства, мысли, вопросы.',
                'en' => 'Everything under the sun — introductions, thoughts, questions.',
            ],
        ],
        [
            'emoji' => '💡',
            'name' => [
                'hy' => 'Գաղափարներ թողարկումների համար',
                'ru' => 'Идеи для выпусков',
                'en' => 'Episode ideas',
            ],
            'description' => [
                'hy' => 'Առաջարկեք թեմա, հյուր կամ ձևաչափ հաջորդ զրույցի համար։',
                'ru' => 'Предложите тему, гостя или формат для следующего разговора.',
                'en' => 'Suggest a topic, a guest or a format for our next conversation.',
            ],
        ],
        [
            'emoji' => '📚',
            'name' => [
                'hy' => 'Գրքեր, ֆիլմեր, երաժշտություն',
                'ru' => 'Книги, фильмы, музыка',
                'en' => 'Books, films, music',
            ],
            'description' => [
                'hy' => 'Ինչ դիտել, կարդալ և լսել թողարկումից հետո։',
                'ru' => 'Что посмотреть, прочитать и послушать после выпуска.',
                'en' => 'What to watch, read and listen to after an episode.',
            ],
        ],
        [
            'emoji' => '🧭',
            'name' => [
                'hy' => 'Ճանապարհորդություններ',
                'ru' => 'Путешествия',
                'en' => 'Travel',
            ],
            'description' => [
                'hy' => 'Երթուղիներ, քաղաքներ, պատմություններ ճանապարհից։',
                'ru' => 'Маршруты, города, истории из дороги.',
                'en' => 'Routes, cities and stories from the road.',
            ],
        ],
    ];

    public function run(): void
    {
        $this->seedAdmin();
        $this->seedSettings();
        $this->seedCategories();

        $result = (new ContentImporter)->run();
        $this->command?->info('Import: '.json_encode($result, JSON_UNESCAPED_UNICODE));
    }

    protected function seedAdmin(): void
    {
        $email = config('site.admin_email');
        $password = config('site.admin_password');
        if (! $email) {
            return;
        }

        $admin = User::where('email', $email)->first();
        if (! $admin) {
            // An admin already exists under another email: move it to ADMIN_EMAIL (password unchanged).
            $admin = User::where('is_admin', true)->orderBy('id')->first();
            if ($admin) {
                $admin->email = $email;
            }
        }
        if (! $admin) {
            if (! $password) {
                return;
            }
            $admin = new User(['email' => $email]);
            $admin->name = config('site.admin_name');
            $admin->password = $password;
        }
        $admin->is_admin = true;
        $admin->save();
        $this->command?->info("Admin: {$email}");
    }

    protected function seedSettings(): void
    {
        foreach (Settings::DEFAULTS as $key => $value) {
            if (! Settings::isTranslatable($key)) {
                Setting::firstOrCreate(['key' => $key], ['value' => $value]);
            }
        }

        // Translatable texts: fill only missing locales; an existing (legacy) Russian value is kept.
        $existing = Setting::query()->pluck('value', 'key');
        foreach (Settings::LOCALIZED_DEFAULTS as $key => $defaults) {
            foreach ($defaults as $locale => $default) {
                $localeKey = "{$key}_{$locale}";
                if (filled($existing[$localeKey] ?? null)) {
                    continue;
                }
                $value = $locale === 'ru' && filled($existing[$key] ?? null) ? $existing[$key] : $default;
                Setting::updateOrCreate(['key' => $localeKey], ['value' => $value]);
            }
        }
        Settings::flush();
    }

    protected function seedCategories(): void
    {
        foreach (self::CATEGORIES as $i => $cat) {
            $category = ForumCategory::firstOrCreate(
                ['slug' => Str::slug($cat['name']['ru'])],
                ['name' => $cat['name']['ru'], 'description' => $cat['description']['ru'], 'emoji' => $cat['emoji'], 'sort' => $i]
            );
            $names = (array) ($category->name_translations ?? []);
            $descriptions = (array) ($category->description_translations ?? []);
            $category->name_translations = $names + $cat['name'];
            $category->description_translations = $descriptions + $cat['description'];
            if ($category->isDirty()) {
                $category->save();
            }
        }
    }
}
