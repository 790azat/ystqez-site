<?php

namespace App\Livewire;

use App\Livewire\Concerns\DarkPagination;
use App\Models\Video;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Выпуски')]
class VideoCatalog extends Component
{
    use DarkPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $type = '';

    #[Url(except: 'new')]
    public string $sort = 'new';

    #[Url(except: '')]
    public string $tag = '';

    public function updated($property): void
    {
        if (in_array($property, ['search', 'type', 'sort', 'tag'], true)) {
            $this->resetPage();
        }
    }

    public function setType(string $type): void
    {
        $this->type = array_key_exists($type, Video::TYPES) ? $type : '';
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'type', 'sort', 'tag');
        $this->resetPage();
    }

    public function render()
    {
        $query = Video::published()
            ->when($this->search !== '', function ($q) {
                $term = '%'.mb_strtolower(trim($this->search)).'%';
                $q->where(fn ($w) => $w->whereRaw('LOWER(title) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(description) LIKE ?', [$term]));
            })
            ->when(array_key_exists($this->type, Video::TYPES), fn ($q) => $q->where('type', $this->type))
            ->when($this->tag !== '', fn ($q) => $q->whereRaw('LOWER(CAST(tags AS TEXT)) LIKE ?', ['%"'.mb_strtolower($this->tag).'"%']));

        $query = match ($this->sort) {
            'popular' => $query->orderByDesc('view_count'),
            'old' => $query->orderBy('published_at'),
            default => $query->orderByDesc('published_at')->orderByDesc('id'),
        };

        return view('livewire.video-catalog', [
            'videos' => $query->paginate(24),
            'counts' => Video::published()->selectRaw('type, COUNT(*) as c')->groupBy('type')->pluck('c', 'type'),
        ]);
    }
}
