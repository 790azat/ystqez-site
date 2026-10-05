                        @if($video->description || $video->tags)
            <div class="card mt-6 p-6" x-data="{ full: false, long: false }" x-init="long = $refs.d.scrollHeight > 260">
                <div x-ref="d" :class="full || !long ? '' : 'max-h-64 overflow-hidden [mask-image:linear-gradient(to_bottom,black_60%,transparent)]'"
                     class="text-[15px] leading-relaxed text-ink-200 break-words">
                    {!! $video->description_html !!}
                </div>
                <button x-cloak x-show="long" @click="full = !full" class="mt-3 text-sm font-bold text-ember-300 hover:text-ember-400"
                        x-text="full ? 'Свернуть' : 'Показать полностью'"></button>
                @if($video->tags)
                    <div class="mt-5 flex flex-wrap gap-2 border-t border-white/5 pt-5">
                        @foreach(array_slice($video->tags, 0, 20) as $tag)
                            <a href="{{ route('videos.index', ['tag' => $tag]) }}" class="chip">#{{ $tag }}</a>
                        @endforeach
                    </div>
                @endif
            </div>
            @endif

