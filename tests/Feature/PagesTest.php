<?php

namespace Tests\Feature;

use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\InstagramPost;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PagesTest extends TestCase
{
    use RefreshDatabase;

    protected function seedContent(): Video
    {
        $video = Video::create([
            'youtube_id' => 'dQw4w9WgXcQ',
            'title' => 'Разговор о свободе',
            'description' => "Таймкоды:\n00:00 Начало\n12:34 Спор\n1:02:03 Итоги",
            'published_at' => now()->subDay(),
            'duration' => 3800,
            'view_count' => 12345,
            'type' => 'video',
            'tags' => ['философия'],
        ]);
        Video::create(['youtube_id' => 'abcdefghijk', 'title' => 'Короткая мысль', 'type' => 'short', 'published_at' => now()]);
        InstagramPost::create(['shortcode' => 'ABC123', 'caption' => 'Горы', 'media_urls' => ['https://example.com/a.jpg']]);
        $cat = ForumCategory::create(['name' => 'Общий', 'slug' => 'obshhii']);
        $user = User::factory()->create();
        $topic = ForumTopic::create(['forum_category_id' => $cat->id, 'user_id' => $user->id, 'title' => 'Первая тема', 'last_post_at' => now()]);
        $topic->posts()->create(['user_id' => $user->id, 'body' => 'Привет всем']);

        return $video;
    }

    public function test_public_pages_load_with_content(): void
    {
        $video = $this->seedContent();

        foreach (['/', '/videos', '/videos?type=short', '/videos?sort=popular&q=свобод', '/chat', '/collab', '/forum',
            '/forum/c/obshhii', '/forum/t/1', '/instagram', '/login', '/register'] as $url) {
            $this->get($url)->assertOk();
        }

        $this->get('/videos/'.$video->slug)
            ->assertOk()
            ->assertSee('youtube-nocookie.com/embed/dQw4w9WgXcQ', false)
            ->assertSee('data-seek="754"', false)
            ->assertSee('data-seek="3723"', false);
    }

    public function test_public_pages_load_when_empty(): void
    {
        foreach (['/', '/videos', '/forum', '/instagram', '/chat'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_video_lookup_by_youtube_id_redirects_to_slug(): void
    {
        $video = $this->seedContent();
        $this->get('/videos/dQw4w9WgXcQ')->assertRedirect('/videos/'.$video->slug);
        $this->get('/videos/nope')->assertNotFound();
    }

    public function test_admin_area_is_protected(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
    }

    public function test_admin_pages_load_for_admin(): void
    {
        $this->seedContent();
        $admin = User::factory()->admin()->create();
        foreach (['/admin', '/admin/videos', '/admin/collabs', '/admin/forum', '/admin/chat', '/admin/comments', '/admin/users', '/admin/settings'] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_register_and_login(): void
    {
        $this->post('/register', [
            'name' => 'Азат', 'email' => 'azat@example.com', 'password' => 'secret-pass-1', 'password_confirmation' => 'secret-pass-1',
        ])->assertRedirect();
        $this->assertAuthenticated();

        auth()->logout();
        $this->post('/login', ['email' => 'azat@example.com', 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->post('/login', ['email' => 'azat@example.com', 'password' => 'secret-pass-1'])->assertRedirect();
        $this->assertAuthenticated();
    }

    public function test_setup_route(): void
    {
        config(['site.setup_token' => null]);
        $this->get('/_setup/anything')->assertNotFound();

        config(['site.setup_token' => 'secret-token', 'site.admin_email' => 'boss@example.com', 'site.admin_password' => 'pass12345']);
        $this->get('/_setup/wrong')->assertNotFound();
        $this->get('/_setup/secret-token')->assertOk()->assertSee('Done');
        $this->get('/_setup/secret-token')->assertOk(); // idempotent

        $this->assertTrue(User::where('email', 'boss@example.com')->value('is_admin'));
        $this->assertSame(1, User::where('email', 'boss@example.com')->count());
        $this->assertGreaterThan(0, ForumCategory::count());
    }
}
