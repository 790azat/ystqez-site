@if(session('ok'))
    <div class="mb-6 flex items-center gap-2 rounded-2xl bg-emerald-500/10 px-4 py-3 text-sm font-semibold text-emerald-300 ring-1 ring-emerald-500/20"><x-icon name="check" class="size-4"/> {{ session('ok') }}</div>
@endif
@if(session('error'))
    <div class="mb-6 rounded-2xl bg-red-500/10 px-4 py-3 text-sm font-semibold text-red-300 ring-1 ring-red-500/20">{{ session('error') }}</div>
@endif
