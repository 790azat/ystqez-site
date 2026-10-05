<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="theme-color" content="#08080a">
@php
    $pageTitle = isset($title) && $title ? __($title).' · Yst Qez' : __('Yst Qez — разговоры друзей о главном');
    $pageDescription = $description ?? \Illuminate\Support\Str::limit(strip_tags((string) setting('hero_subtitle')), 160);
    $ogImage = $ogImage ?? media_url(setting('channel_banner')) ?? media_url(setting('channel_avatar'));
@endphp
<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $pageDescription }}">
<meta property="og:site_name" content="Yst Qez">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $pageDescription }}">
<meta property="og:type" content="website">
<meta property="og:locale" content="{{ ['hy' => 'hy_AM', 'ru' => 'ru_RU', 'en' => 'en_US'][app()->getLocale()] ?? 'hy_AM' }}">
<meta property="og:url" content="{{ url()->current() }}">
@if($ogImage)<meta property="og:image" content="{{ $ogImage }}">@endif
<meta name="twitter:card" content="summary_large_image">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="alternate icon" href="/favicon.ico">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://i.ytimg.com">
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Noto+Sans+Armenian:wght@400;500;600;700;800&family=Unbounded:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])
