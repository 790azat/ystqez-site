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
    public function run(): void
    {
        $email = config('site.admin_email');
        $password = config('site.admin_password');
        if ($email && $password) {
            $admin = User::firstOrNew(['email' => $email]);
            if (! $admin->exists) {
                $admin->name = config('site.admin_name');
                $admin->password = $password;
            }
            $admin->is_admin = true;
            $admin->save();
            $this->command?->info("Админ: {$email}");
        }

        foreach (Settings::DEFAULTS as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
        Settings::flush();

        $categories = [
            ['💬', 'Общий разговор', 'Обо всём на свете — знакомства, мысли, вопросы.'],
            ['💡', 'Идеи для выпусков', 'Предложите тему, гостя или формат для следующего разговора.'],
            ['📚', 'Книги, фильмы, музыка', 'Что посмотреть, прочитать и послушать после выпуска.'],
            ['🧭', 'Путешествия', 'Маршруты, города, истории из дороги.'],
        ];
        foreach ($categories as $i => [$emoji, $name, $desc]) {
            ForumCategory::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'description' => $desc, 'emoji' => $emoji, 'sort' => $i]
            );
        }

        $result = (new ContentImporter)->run();
        $this->command?->info('Импорт: '.json_encode($result, JSON_UNESCAPED_UNICODE));
    }
}
