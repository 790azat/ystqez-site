<?php

namespace App\Livewire\Concerns;

trait AdminOnly
{
    /** Runs on every request (initial + each Livewire update). */
    public function bootAdminOnly(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
    }
}
