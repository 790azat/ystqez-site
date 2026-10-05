<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\DarkPagination;
use App\Models\Video;
use App\Services\YouTubeService;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use App\Livewire\Concerns\AdminOnly;
use Livewire\Component;
use Throwable;

#[Layout('components.layouts.admin')]
#[Title('Видео — Админка')]
class Videos extends Component
{
    use AdminOnly, DarkPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    public ?int $editingId = null;

    public bool $showForm = false;

    public array $form = [];

    public function mount(): void
    {
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->form = [
            'url' => '', 'title' => '', 'description' => '', 'type' => 'video',
            'published_at' => '', 'duration' => '', 'view_count' => '', 'thumbnail' => '',
            'tags' => '', 'is_featured' => false, 'is_published' => true,
        ];
        $this->resetErrorBag();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->editingId = null;
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $v = Video::findOrFail($id);
        $this->editingId = $v->id;
        $this->form = [
            'url' => $v->youtube_id,
            'title' => $v->title,
            'description' => (string) $v->description,
            'type' => $v->type,
            'published_at' => $v->published_at?->format('Y-m-d\TH:i') ?? '',
            'duration' => (string) ($v->duration ?? ''),
            'view_count' => (string) $v->view_count,
            'thumbnail' => (string) $v->thumbnail,
            'tags' => implode(', ', $v->tags ?? []),
            'is_featured' => $v->is_featured,
            'is_published' => $v->is_published,
        ];
        $this->resetErrorBag();
        $this->showForm = true;
    }

    public function cancel(): void
    {
        $this->showForm = false;
        $this->editingId = null;
    }

    public function save(YouTubeService $yt): void
    {
        $this->validate([
            'form.url' => ['required', 'string', 'max:300'],
            'form.title' => ['nullable', 'string', 'max:250'],
            'form.description' => ['nullable', 'string', 'max:20000'],
            'form.type' => ['required', Rule::in(array_keys(Video::TYPES))],
            'form.published_at' => ['nullable', 'date'],
            'form.duration' => ['nullable', 'integer', 'min:0'],
            'form.view_count' => ['nullable', 'integer', 'min:0'],
            'form.thumbnail' => ['nullable', 'url', 'max:1000'],
            'form.tags' => ['nullable', 'string', 'max:1000'],
        ], [], [
            'form.url' => __('ссылка или ID'), 'form.title' => __('название'), 'form.type' => __('тип'),
            'form.thumbnail' => __('обложка'), 'form.duration' => __('длительность'), 'form.view_count' => __('просмотры'),
        ]);

        $youtubeId = Video::extractYoutubeId($this->form['url']);
        if (! $youtubeId) {
            $this->addError('form.url', __('Не удалось распознать YouTube-ссылку или ID.'));

            return;
        }
        $duplicate = Video::where('youtube_id', $youtubeId)->when($this->editingId, fn ($q) => $q->whereKeyNot($this->editingId))->exists();
        if ($duplicate) {
            $this->addError('form.url', __('Это видео уже есть в каталоге.'));

            return;
        }

        $video = $this->editingId ? Video::findOrFail($this->editingId) : new Video;
        $title = trim($this->form['title']);
        $thumbnail = trim($this->form['thumbnail']);
        $note = '';

        if (! $video->exists && ($title === '' || $thumbnail === '')) {
            $meta = $yt->oembed($youtubeId);
            if ($meta) {
                $title = $title ?: (string) $meta['title'];
                $thumbnail = $thumbnail ?: (string) $meta['thumbnail'];
            } else {
                $note = ' '.__('(YouTube недоступен — данные не подтянулись, заполните вручную)');
            }
        }
        if (str_contains($this->form['url'], '/shorts/') && ! $video->exists) {
            $this->form['type'] = 'short';
        }

        $video->fill([
            'youtube_id' => $youtubeId,
            'title' => $title !== '' ? $title : 'Video '.$youtubeId,
            'description' => $this->form['description'] ?: null,
            'type' => $this->form['type'],
            'published_at' => $this->form['published_at'] ? Carbon::parse($this->form['published_at']) : ($video->published_at ?? now()),
            'duration' => $this->form['duration'] !== '' ? (int) $this->form['duration'] : null,
            'view_count' => (int) ($this->form['view_count'] ?: 0),
            'thumbnail' => $thumbnail ?: null,
            'tags' => array_values(array_filter(array_map('trim', explode(',', $this->form['tags'])))) ?: null,
            'is_featured' => (bool) $this->form['is_featured'],
            'is_published' => (bool) $this->form['is_published'],
        ]);
        $video->save();

        $this->showForm = false;
        $this->editingId = null;
        session()->flash('ok', __('Видео сохранено').$note);
    }

    public function toggleFeatured(int $id): void
    {
        $v = Video::findOrFail($id);
        $v->update(['is_featured' => ! $v->is_featured]);
    }

    public function togglePublished(int $id): void
    {
        $v = Video::findOrFail($id);
        $v->update(['is_published' => ! $v->is_published]);
    }

    public function delete(int $id): void
    {
        Video::whereKey($id)->delete();
        session()->flash('ok', __('Видео удалено'));
    }

    public function sync(YouTubeService $yt): void
    {
        try {
            $r = $yt->syncRss(config('site.youtube_channel_id'));
            session()->flash('ok', __('Синхронизация завершена: новых — :created, обновлено — :updated.', ['created' => $r['created'], 'updated' => $r['updated']]));
        } catch (Throwable $e) {
            session()->flash('error', __('Не удалось синхронизироваться с YouTube:').' '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.videos', [
            'videos' => Video::query()
                ->when($this->search !== '', fn ($q) => $q->where(fn ($w) => $w
                    ->whereRaw('LOWER(title) LIKE ?', ['%'.mb_strtolower($this->search).'%'])
                    ->orWhere('youtube_id', $this->search)))
                ->latest('published_at')->latest('id')->paginate(20),
        ]);
    }
}
