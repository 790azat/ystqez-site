<div x-data="{ show: false, message: '', timer: null }"
     @toast.window="message = $event.detail.message ?? $event.detail[0]?.message ?? {{ \Illuminate\Support\Js::from(__('Готово')) }}; show = true; clearTimeout(timer); timer = setTimeout(() => show = false, 2600)"
     class="pointer-events-none fixed inset-x-0 bottom-6 z-[60] flex justify-center px-4">
    <div x-cloak x-show="show" x-transition.opacity.duration.200ms
         class="pointer-events-auto inline-flex items-center gap-2 rounded-full bg-ink-800 px-5 py-3 text-sm font-semibold shadow-2xl ring-1 ring-white/10">
        <x-icon name="check" class="size-4 text-ember-400"/>
        <span x-text="message"></span>
    </div>
</div>
@if(session('status'))
    <div x-data x-init="$nextTick(() => window.dispatchEvent(new CustomEvent('toast', { detail: { message: @js(session('status')) } })))"></div>
@endif
