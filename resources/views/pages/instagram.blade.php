<x-layouts.app title="Instagram">
@php
    $avatar = media_url(setting('instagram_avatar')) ?? media_url(setting('channel_avatar'));
    $followers = setting('instagram_followers');
@endphp
<div class="container-yq pt-10">
    <div class="card noise relative overflow-hidden p-6 sm:p-10">
        <div class="absolute -right-20 -top-20 size-72 rounded-full bg-gradient-to-br from-fuchsia-500/20 via-ember-500/20 to-sun-500/20 blur-3xl"></div>
        <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center">
            <div class="rounded-full bg-gradient-to-tr from-sun-500 via-ember-500 to-fuchsia-500 p-[3px]">
                <div class="grid size-24 place-items-center overflow-hidden rounded-full bg-ink-900">
                    @if($avatar)
                        <img src="{{ $avatar }}" alt="" class="h-full w-full object-cover" referrerpolicy="no-referrer" data-hide-on-error>
                    @else
                        <x-icon name="instagram" class="size-10 text-ink-400"/>
                    @endif
                </div>
            </div>
            <div class="flex-1">
                <h1 class="text-3xl font-semibold">{{ '@'.config('site.instagram_handle') }}</h1>
                <p class="mt-2 max-w-xl whitespace-pre-line text-sm text-ink-300">{{ setting('instagram_bio', __('Кадры из путешествий, закулисье выпусков и моменты между разговорами.')) }}</p>
                <div class="mt-3 flex gap-5 text-sm text-ink-400">
                    <span><b class="text-white">{{ $posts->total() }}</b> {{ trans_choice('публикация|публикации|публикаций', $posts->total()) }}</span>
                    @if($followers)<span><b class="text-white">{{ compact_num((int) $followers) }}</b> {{ trans_choice('подписчик|подписчика|подписчиков', (int) $followers) }}</span>@endif
                </div>
            </div>
            <a href="{{ setting('instagram_url') }}" target="_blank" rel="noopener" class="btn btn-primary"><x-icon name="instagram" class="size-4"/> {{ __('Подписаться') }}</a>
        </div>
    </div>

    <div class="mt-10">
        @if($posts->isNotEmpty())
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                @foreach($posts as $post)
                    @include('partials.insta-tile', ['post' => $post])
                @endforeach
            </div>
            {{ $posts->links() }}
        @else
            <x-empty icon="image" title="{{ __('Публикации скоро появятся') }}" text="{{ __('А пока все свежие кадры — в нашем профиле.') }}">
                <a href="{{ setting('instagram_url') }}" target="_blank" rel="noopener" class="btn btn-ghost"><x-icon name="instagram" class="size-4"/> {{ __('Открыть Instagram') }}</a>
            </x-empty>
        @endif
    </div>
</div>
</x-layouts.app>
