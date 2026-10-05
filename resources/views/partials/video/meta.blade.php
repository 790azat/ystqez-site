            <h1 class="text-2xl font-semibold leading-tight sm:text-3xl">{{ $video->display_title }}</h1>
            <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-ink-400">
                @if($video->published_at)<span class="inline-flex items-center gap-1.5"><x-icon name="calendar" class="size-4"/> {{ $video->published_at->translatedFormat('j F Y') }}</span>@endif
                @if($video->view_count)<span class="inline-flex items-center gap-1.5"><x-icon name="eye" class="size-4"/> {{ number_format($video->view_count, 0, ',', ' ') }} {{ \App\Support\Ru::plural($video->view_count, ['просмотр', 'просмотра', 'просмотров']) }}</span>@endif
                @if($video->duration_human)<span class="inline-flex items-center gap-1.5"><x-icon name="clock" class="size-4"/> {{ $video->duration_human }}</span>@endif
                <span class="badge bg-white/5 text-ink-300 ring-1 ring-white/10">{{ $video->type_label }}</span>
            </div>

