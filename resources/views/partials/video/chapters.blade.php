            @if($chapters)
                <div class="card p-5">
                    <h2 class="flex items-center gap-2 font-sans text-sm font-bold uppercase tracking-wider text-ink-400"><x-icon name="list" class="size-4"/> Таймкоды</h2>
                    <ol class="mt-3 max-h-80 space-y-0.5 overflow-y-auto pr-1">
                        @foreach($chapters as $ch)
                            <li>
                                <button type="button" data-seek="{{ $ch['seconds'] }}" class="flex w-full items-start gap-3 rounded-xl px-2.5 py-2 text-left text-sm transition hover:bg-white/5">
                                    <span class="mt-px shrink-0 font-mono text-xs font-bold text-ember-300">{{ $ch['time'] }}</span>
                                    <span class="text-ink-200">{{ $ch['label'] }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ol>
                </div>
            @endif

