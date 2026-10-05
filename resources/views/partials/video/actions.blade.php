                        <div class="mt-6 flex flex-wrap items-center gap-2.5">
                <livewire:video-likes :video-id="$video->id" />
                <div class="flex items-center gap-1.5 rounded-full bg-white/5 p-1 ring-1 ring-white/10">
                    <a href="https://t.me/share/url?url={{ $shareUrl }}&text={{ $shareText }}" target="_blank" rel="noopener" class="grid size-9 place-items-center rounded-full hover:bg-white/10" title="Telegram"><x-icon name="telegram" class="size-4"/></a>
                    <a href="https://vk.com/share.php?url={{ $shareUrl }}" target="_blank" rel="noopener" class="grid size-9 place-items-center rounded-full text-xs font-black hover:bg-white/10" title="{{ __('ВКонтакте') }}">VK</a>
                    <a href="https://wa.me/?text={{ $shareText }}%20{{ $shareUrl }}" target="_blank" rel="noopener" class="grid size-9 place-items-center rounded-full hover:bg-white/10" title="WhatsApp"><x-icon name="whatsapp" class="size-4"/></a>
                    <a href="https://x.com/intent/post?url={{ $shareUrl }}&text={{ $shareText }}" target="_blank" rel="noopener" class="grid size-9 place-items-center rounded-full hover:bg-white/10" title="X"><x-icon name="x-social" class="size-3.5"/></a>
                    <button type="button" data-copy="{{ route('videos.show', $video->slug) }}" data-copied="{{ __('Ссылка скопирована') }}" class="grid size-9 place-items-center rounded-full hover:bg-white/10" title="{{ __('Скопировать ссылку') }}"><x-icon name="link" class="size-4"/></button>
                </div>
                <a href="{{ $video->youtube_url }}" target="_blank" rel="noopener" class="btn btn-ghost ml-auto"><x-icon name="youtube" class="size-4 text-ember-400"/> {{ __('Открыть на YouTube') }}</a>
            </div>

