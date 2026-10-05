<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\DarkPagination;
use App\Models\ForumCategory;
use App\Models\ForumTopic;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Livewire\Concerns\AdminOnly;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Форум — Админка')]
class Forum extends Component
{
    use AdminOnly, DarkPagination;

    public string $catName = '';

    public string $catDescription = '';

    public string $catEmoji = '💬';

    public string $catNameHy = '';

    public string $catNameEn = '';

    public function togglePin(int $id): void
    {
        $t = ForumTopic::findOrFail($id);
        $t->update(['is_pinned' => ! $t->is_pinned]);
    }

    public function toggleLock(int $id): void
    {
        $t = ForumTopic::findOrFail($id);
        $t->update(['is_locked' => ! $t->is_locked]);
    }

    public function deleteTopic(int $id): void
    {
        ForumTopic::whereKey($id)->delete();
        session()->flash('ok', __('Тема удалена'));
    }

    public function addCategory(): void
    {
        $this->validate([
            'catName' => ['required', 'string', 'max:120'],
            'catDescription' => ['nullable', 'string', 'max:500'],
            'catEmoji' => ['nullable', 'string', 'max:16'],
            'catNameHy' => ['nullable', 'string', 'max:120'],
            'catNameEn' => ['nullable', 'string', 'max:120'],
        ], [], ['catName' => __('название')]);

        $slug = Str::slug($this->catName) ?: 'cat';
        $base = $slug;
        $i = 2;
        while (ForumCategory::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }
        ForumCategory::create([
            'name' => $this->catName, 'slug' => $slug, 'description' => $this->catDescription,
            'emoji' => $this->catEmoji, 'sort' => (int) ForumCategory::max('sort') + 1,
            'name_translations' => array_filter([
                'hy' => trim($this->catNameHy), 'ru' => trim($this->catName), 'en' => trim($this->catNameEn),
            ], 'strlen') ?: null,
        ]);
        $this->reset('catName', 'catNameHy', 'catNameEn', 'catDescription');
        session()->flash('ok', __('Категория добавлена'));
    }

    public function deleteCategory(int $id): void
    {
        ForumCategory::whereKey($id)->delete();
        session()->flash('ok', __('Категория удалена вместе с темами'));
    }

    public function render()
    {
        return view('livewire.admin.forum', [
            'categories' => ForumCategory::withCount('topics')->orderBy('sort')->get(),
            'topics' => ForumTopic::with(['category', 'user'])->orderByDesc('is_pinned')->latest('last_post_at')->paginate(25),
        ]);
    }
}
