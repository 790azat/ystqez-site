<?php

namespace Tests\Feature;

use App\Livewire\ChatRoom;
use App\Livewire\CollabForm;
use App\Livewire\Forum\CategoryShow;
use App\Livewire\Forum\TopicShow;
use App\Livewire\VideoComments;
use App\Livewire\VideoLikes;
use App\Models\ChatMessage;
use App\Models\CollabRequest;
use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InteractionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_collab_form_validates_and_stores(): void
    {
        Livewire::test(CollabForm::class)
            ->call('submit')
            ->assertHasErrors(['name', 'contact', 'message']);

        Livewire::test(CollabForm::class)
            ->set('name', 'Бренд')
            ->set('contact', 'not a contact!!')
            ->set('message', 'Хотим интеграцию в выпуск')
            ->call('submit')
            ->assertHasErrors(['contact']);

        Livewire::test(CollabForm::class)
            ->set('name', 'Иван')
            ->set('contact', '@ivan_brand')
            ->set('company', 'ООО Ромашка')
            ->set('type', 'integration')
            ->set('budget', '30 000 – 100 000 ₽')
            ->set('message', 'Хотим нативную интеграцию в выпуск про путешествия.')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('sent', true)
            ->assertSee('Заявка отправлена');

        $this->assertDatabaseHas('collab_requests', ['name' => 'Иван', 'type' => 'integration', 'status' => 'new']);
    }

    public function test_collab_honeypot_drops_spam(): void
    {
        Livewire::test(CollabForm::class)
            ->set('name', 'Bot')->set('contact', 'bot@example.com')->set('message', 'Buy cheap stuff now!!!')
            ->set('website', 'http://spam')
            ->call('submit')
            ->assertSet('sent', true);
        $this->assertSame(0, CollabRequest::count());
    }

    public function test_guest_chat_requires_nickname_then_posts(): void
    {
        Livewire::test(ChatRoom::class)
            ->set('body', 'Привет')
            ->call('send')
            ->assertHasErrors('nickname');

        Livewire::test(ChatRoom::class)
            ->set('nickname', 'Странник')
            ->call('setNickname')
            ->assertHasNoErrors()
            ->set('body', 'Привет, чат!')
            ->call('send')
            ->assertHasNoErrors()
            ->assertSee('Привет, чат!');

        $this->assertDatabaseHas('chat_messages', ['nickname' => 'Странник', 'body' => 'Привет, чат!']);
    }

    public function test_chat_rate_limit_and_admin_delete(): void
    {
        $user = User::factory()->create(['name' => 'Тимур']);
        $c = Livewire::actingAs($user)->test(ChatRoom::class);
        for ($i = 1; $i <= 5; $i++) {
            $c->set('body', "msg $i")->call('send')->assertHasNoErrors();
        }
        $c->set('body', 'too many')->call('send')->assertHasErrors('body');
        $this->assertSame(5, ChatMessage::count());

        $id = ChatMessage::first()->id;
        Livewire::actingAs($user)->test(ChatRoom::class)->call('delete', $id)->assertForbidden();
        Livewire::actingAs(User::factory()->admin()->create())->test(ChatRoom::class)->call('delete', $id);
        $this->assertSame(4, ChatMessage::count());
    }

    public function test_forum_topic_create_and_reply(): void
    {
        $cat = ForumCategory::create(['name' => 'Идеи', 'slug' => 'idei']);
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(CategoryShow::class, ['category' => $cat])
            ->set('title', 'Позовите философа')
            ->set('body', 'Было бы круто поговорить о стоицизме.')
            ->call('create')
            ->assertHasNoErrors()
            ->assertRedirect();

        $topic = ForumTopic::firstOrFail();
        $this->assertSame('Позовите философа', $topic->title);
        $this->assertSame(1, $topic->posts()->count());

        Livewire::actingAs($user)->test(TopicShow::class, ['topic' => $topic])
            ->set('body', 'Поддерживаю!')
            ->call('reply')
            ->assertHasNoErrors()
            ->assertSee('Поддерживаю!');
        $this->assertSame(1, $topic->fresh()->replies_count);

        $topic->update(['is_locked' => true]);
        Livewire::actingAs($user)->test(TopicShow::class, ['topic' => $topic->fresh()])
            ->set('body', 'Ещё ответ')
            ->call('reply')
            ->assertHasErrors('body');
    }

    public function test_guest_cannot_create_topic(): void
    {
        $cat = ForumCategory::create(['name' => 'Идеи', 'slug' => 'idei']);
        Livewire::test(CategoryShow::class, ['category' => $cat])
            ->call('openForm')
            ->assertRedirect(route('login'));
        Livewire::test(CategoryShow::class, ['category' => $cat])
            ->set('title', 'Тема гостя')->set('body', 'Текст гостя')
            ->call('create')
            ->assertForbidden();
    }

    public function test_guest_comment_and_like(): void
    {
        $video = Video::create(['youtube_id' => 'dQw4w9WgXcQ', 'title' => 'Выпуск']);

        Livewire::test(VideoComments::class, ['videoId' => $video->id])
            ->set('body', 'Отличный выпуск')
            ->call('post')
            ->assertHasErrors('name')
            ->set('name', 'Гость')
            ->call('post')
            ->assertHasNoErrors()
            ->assertSee('Отличный выпуск');
        $this->assertDatabaseHas('video_comments', ['guest_name' => 'Гость']);

        Livewire::test(VideoLikes::class, ['videoId' => $video->id])
            ->call('toggle')->assertSet('liked', true)->assertSet('count', 1)
            ->call('toggle')->assertSet('liked', false)->assertSet('count', 0);
    }

    public function test_admin_adds_video_by_url_with_oembed_and_sync_fails_gracefully(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            'www.youtube.com/oembed*' => \Illuminate\Support\Facades\Http::response(['title' => 'Из oEmbed', 'thumbnail_url' => 'https://i.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg']),
            'www.youtube.com/feeds/*' => \Illuminate\Support\Facades\Http::response('blocked', 403),
        ]);
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)->test(\App\Livewire\Admin\Videos::class)
            ->call('create')
            ->set('form.url', 'https://youtu.be/dQw4w9WgXcQ')
            ->call('save')
            ->assertHasNoErrors()
            ->call('sync')
            ->assertSee('Не удалось синхронизироваться с YouTube');

        $this->assertSame('Из oEmbed', Video::where('youtube_id', 'dQw4w9WgXcQ')->value('title'));

        Livewire::actingAs(User::factory()->create())->test(\App\Livewire\Admin\Videos::class)->assertForbidden();
    }
}
