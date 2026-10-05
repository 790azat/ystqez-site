<?php

namespace Tests\Feature;

use App\Livewire\CollabForm;
use App\Livewire\VideoCatalog;
use App\Models\ForumCategory;
use App\Models\Setting;
use App\Models\User;
use App\Support\Settings;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Settings::flush();
        parent::tearDown();
    }

    public function test_default_locale_is_armenian(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<html lang="hy"', false)
            ->assertSee('Դիտել թողարկումները')
            ->assertSee(Settings::LOCALIZED_DEFAULTS['hero_title']['hy'])
            ->assertSee('aria-current="true"', false)
            ->assertDontSee('Смотреть выпуски');
    }

    public function test_switching_to_english_sets_cookie_and_session(): void
    {
        $this->from('/videos')->get('/lang/en')
            ->assertRedirect('/videos')
            ->assertPlainCookie('locale', 'en')
            ->assertSessionHas('locale', 'en');

        // Session keeps the choice on the next request.
        $this->get('/')
            ->assertOk()
            ->assertSee('<html lang="en"', false)
            ->assertSee('Watch episodes')
            ->assertSee(Settings::LOCALIZED_DEFAULTS['hero_title']['en'])
            ->assertDontSee('Դիտել թողարկումները');
    }

    public function test_locale_cookie_alone_is_respected(): void
    {
        $this->withUnencryptedCookie('locale', 'ru')->get('/')
            ->assertOk()
            ->assertSee('<html lang="ru"', false)
            ->assertSee('Смотреть выпуски');

        $this->withUnencryptedCookie('locale', 'en')->get('/forum')
            ->assertOk()
            ->assertSee('A place for unhurried conversations');
    }

    public function test_unknown_locale_is_rejected(): void
    {
        $this->get('/lang/de')->assertNotFound();
        $this->withUnencryptedCookie('locale', 'de')->get('/')->assertSee('<html lang="hy"', false);
    }

    public function test_redirect_back_never_leaves_the_site(): void
    {
        $this->from('https://evil.example/phish')->get('/lang/ru')->assertRedirect(route('home'));
    }

    public function test_livewire_full_page_component_renders_in_chosen_locale(): void
    {
        $this->withUnencryptedCookie('locale', 'en')->get('/videos')
            ->assertOk()
            ->assertSee('Search titles and descriptions…')
            ->assertSee('Episodes');

        $this->withUnencryptedCookie('locale', 'hy')->get('/collab')
            ->assertOk()
            ->assertSee('Ուղարկել հայտը');
    }

    public function test_livewire_component_and_validation_use_current_locale(): void
    {
        app()->setLocale('en');
        Livewire::test(VideoCatalog::class)->assertSee('The catalogue is empty for now')->assertDontSee('Каталог пока пуст');

        app()->setLocale('hy');
        Livewire::test(CollabForm::class)
            ->set('name', '')
            ->call('submit')
            ->assertHasErrors(['name' => 'required'])
            ->assertSee('Լրացրեք «անուն» դաշտը։');
    }

    public function test_locale_middleware_is_persistent_for_livewire_updates(): void
    {
        // A real Livewire update request (POST /livewire/update) re-renders in the page's locale
        // (Livewire keeps the locale in the component snapshot; the middleware runs on updates too).
        $page = $this->withUnencryptedCookie('locale', 'en')->get('/collab')->assertOk()->getContent();
        preg_match_all('/wire:snapshot="([^"]+)"/', $page, $m);
        $snapshot = collect($m[1])->map(fn ($s) => html_entity_decode($s, ENT_QUOTES))
            ->first(fn ($s) => str_contains($s, '"name":"collab-form"'));
        $this->assertNotNull($snapshot);

        app()->setLocale('ru');
        $json = $this->withCredentials()->withUnencryptedCookie('locale', 'en')
            ->withHeaders(['X-Livewire' => 'true'])
            ->postJson(Livewire::getUpdateUri(), ['components' => [[
                'snapshot' => $snapshot,
                'updates' => ['name' => ''],
                'calls' => [['path' => '', 'method' => 'submit', 'params' => []]],
            ]]])
            ->assertOk()
            ->json('components.0.effects.html');
        $this->assertStringContainsString('Please fill in the name.', $json);
        $this->assertStringContainsString('Send request', $json);
        $this->assertStringNotContainsString('Ուղարկել հայտը', $json);

        $this->assertContains(\App\Http\Middleware\SetLocale::class, Livewire::getPersistentMiddleware());
        $this->assertContains(\App\Http\Middleware\SetLocale::class, app('router')->getMiddlewareGroups()['web']);
    }

    public function test_dates_and_numbers_are_localized(): void
    {
        $date = \Illuminate\Support\Carbon::create(2026, 3, 8);

        app()->setLocale('en');
        $this->assertSame('March 8, 2026', fdate($date, 'long'));
        $this->assertSame('12.5K', compact_num(12500));
        $this->assertSame('1 view', trans_choice(':count просмотр|:count просмотра|:count просмотров', 1));

        app()->setLocale('ru');
        $this->assertSame('8 марта 2026', fdate($date, 'long'));
        $this->assertSame('12,5 тыс.', compact_num(12500));
        $this->assertSame('5 просмотров', trans_choice(':count просмотр|:count просмотра|:count просмотров', 5));

        app()->setLocale('hy');
        $this->assertMatchesRegularExpression('/^8 \p{Armenian}+ 2026$/u', fdate($date, 'long'));
        $this->assertSame('5 դիտում', trans_choice(':count просмотр|:count просмотра|:count просмотров', 5));
    }

    public function test_translatable_settings_fall_back_to_russian(): void
    {
        Setting::create(['key' => 'hero_title', 'value' => 'Старый заголовок']);
        Setting::create(['key' => 'hero_title_en', 'value' => 'English title']);
        Settings::flush();

        app()->setLocale('en');
        $this->assertSame('English title', setting('hero_title'));
        app()->setLocale('hy');
        $this->assertSame('Старый заголовок', setting('hero_title'));

        Setting::create(['key' => 'hero_title_hy', 'value' => 'Հայերեն վերնագիր']);
        Settings::flush();
        $this->assertSame('Հայերեն վերնագիր', setting('hero_title'));
    }

    public function test_seeder_fills_missing_locales_and_keeps_existing_russian(): void
    {
        Setting::create(['key' => 'hero_title', 'value' => 'Мой заголовок']);
        Setting::create(['key' => 'about_text_en', 'value' => 'Custom about']);

        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class); // idempotent

        $values = Setting::pluck('value', 'key');
        $this->assertSame('Мой заголовок', $values['hero_title_ru']);
        $this->assertSame(Settings::LOCALIZED_DEFAULTS['hero_title']['hy'], $values['hero_title_hy']);
        $this->assertSame('Custom about', $values['about_text_en']);
        $this->assertSame(1, Setting::where('key', 'hero_title_hy')->count());

        $cat = ForumCategory::where('slug', 'putesestviia')->firstOrFail();
        app()->setLocale('hy');
        $this->assertSame('Ճանապարհորդություններ', $cat->name);
        app()->setLocale('en');
        $this->assertSame('Travel', $cat->name);
        $this->assertSame('Routes, cities and stories from the road.', $cat->description);
        $this->assertSame(4, ForumCategory::count());
    }

    public function test_forum_category_without_translations_uses_stored_name(): void
    {
        $cat = ForumCategory::create(['name' => 'Свой раздел', 'slug' => 'svoi']);
        app()->setLocale('en');
        $this->assertSame('Свой раздел', $cat->fresh()->name);

        $cat->update(['name_translations' => ['hy' => 'Իմ բաժինը']]);
        $this->assertSame('Свой раздел', $cat->fresh()->name, 'falls back to stored name when en and ru are missing');
        app()->setLocale('hy');
        $this->assertSame('Իմ բաժինը', $cat->fresh()->name);
    }

    public function test_admin_settings_form_has_a_field_per_locale(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\Settings::class)
            ->assertSee('ՀԱՅ')->assertSee('ENG')
            ->set('values.hero_title_hy', 'Նոր վերնագիր')
            ->set('values.hero_title_ru', 'Новый заголовок')
            ->call('save');

        Settings::flush();
        app()->setLocale('hy');
        $this->assertSame('Նոր վերնագիր', setting('hero_title'));
        app()->setLocale('ru');
        $this->assertSame('Новый заголовок', setting('hero_title'));
    }

    public function test_every_translation_key_has_armenian_and_english_text(): void
    {
        $hy = json_decode(file_get_contents(lang_path('hy.json')), true);
        $en = json_decode(file_get_contents(lang_path('en.json')), true);
        $files = array_merge(
            glob(app_path('{,*/,*/*/,*/*/*/}*.php'), GLOB_BRACE),
            glob(resource_path('views/{,*/,*/*/,*/*/*/}*.blade.php'), GLOB_BRACE),
        );
        $missing = [];
        foreach ($files as $file) {
            preg_match_all("/(?:__|trans_choice)\\(\\s*'((?:[^'\\\\]|\\\\.)*)'/u", file_get_contents($file), $m);
            foreach ($m[1] as $key) {
                $key = str_replace("\\'", "'", $key);
                if (! preg_match('/\p{Cyrillic}/u', $key)) {
                    continue;
                }
                if (! isset($hy[$key]) || ! isset($en[$key])) {
                    $missing[] = basename($file).': '.$key;
                }
            }
        }
        $this->assertSame([], $missing);

        foreach ([$hy, $en] as $dict) {
            foreach ($dict as $key => $value) {
                $this->assertDoesNotMatchRegularExpression('/\p{Cyrillic}/u', str_replace('₽', '', $value), "Untranslated: $key");
            }
        }
    }
}
