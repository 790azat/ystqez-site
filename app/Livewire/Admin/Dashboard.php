<?php

namespace App\Livewire\Admin;

use App\Models\ChatMessage;
use App\Models\CollabRequest;
use App\Models\ForumPost;
use App\Models\ForumTopic;
use App\Models\InstagramPost;
use App\Models\User;
use App\Models\Video;
use App\Models\VideoComment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Livewire\Concerns\AdminOnly;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Админка')]
class Dashboard extends Component
{
    use AdminOnly;
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'stats' => [
                [__('Видео'), Video::count(), route('admin.videos')],
                [__('Заявки (новые)'), CollabRequest::where('status', 'new')->count(), route('admin.collabs')],
                [__('Пользователи'), User::count(), route('admin.users')],
                [__('Темы форума'), ForumTopic::count(), route('admin.forum')],
                [__('Сообщения форума'), ForumPost::count(), route('admin.forum')],
                [__('Чат (24 ч)'), ChatMessage::where('created_at', '>=', now()->subDay())->count(), route('admin.chat')],
                [__('Комментарии'), VideoComment::count(), route('admin.comments')],
                [__('Посты Instagram'), InstagramPost::count(), route('instagram')],
            ],
            'collabs' => CollabRequest::latest()->take(5)->get(),
            'comments' => VideoComment::with(['video', 'user'])->latest()->take(5)->get(),
        ]);
    }
}
