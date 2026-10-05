<a href="{{ $post->url }}" target="_blank" rel="noopener" class="group relative block aspect-square overflow-hidden rounded-2xl bg-ink-800 ring-1 ring-white/5">
    <div class="absolute inset-0 grid place-items-center bg-gradient-to-br from-ember-500/10 to-sun-500/5">
        <x-icon name="instagram" class="size-8 text-ink-600"/>
    </div>
    @if($post->image_url)
        <img src="{{ $post->image_url }}" alt="{{ \Illuminate\Support\Str::limit($post->caption, 80) }}" loading="lazy" referrerpolicy="no-referrer" data-hide-on-error
             class="relative h-full w-full object-cover transition duration-500 group-hover:scale-105">
    @endif
    @if($post->is_video)
        <span class="absolute right-2.5 top-2.5 grid size-7 place-items-center rounded-full bg-black/60 backdrop-blur"><x-icon name="play-fill" class="size-3.5"/></span>
    @endif
    <div class="absolute inset-0 flex flex-col justify-end bg-gradient-to-t from-black/85 via-black/30 to-transparent p-3 opacity-0 transition group-hover:opacity-100">
        @if($post->likes)<p class="flex items-center gap-1 text-xs font-bold"><x-icon name="heart-fill" class="size-3.5 text-ember-400"/> {{ \App\Support\Ru::compact($post->likes) }}</p>@endif
        @if($post->caption)<p class="mt-1 line-clamp-3 text-xs text-ink-200">{{ $post->caption }}</p>@endif
    </div>
</a>
