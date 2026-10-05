<?php

namespace App\Livewire\Concerns;

use Livewire\WithPagination;

trait DarkPagination
{
    use WithPagination;

    public function paginationView(): string
    {
        return 'partials.livewire-pagination';
    }

    public function paginationSimpleView(): string
    {
        return 'partials.livewire-pagination';
    }
}
