<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="canonical" href="{{ $meta['url'] ?? url('/') }}">

    <title>{{ $meta['title'] ?? '諾摩浪浪' }}</title>
    <meta name="description" content="{{ $meta['description'] ?? '' }}">
    <meta name="language" content="zh-TW">
    <meta name="theme-color" content="#2c5282">

    <!-- Icons -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:locale" content="zh_TW">
    <meta property="og:url" content="{{ $meta['url'] ?? url('/') }}">
    <meta property="og:title" content="{{ $meta['title'] ?? '諾摩浪浪' }}">
    <meta property="og:description" content="{{ $meta['description'] ?? '' }}">
    <meta property="og:image" content="{{ $meta['image'] ?? asset('images/og-image.jpg') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ $meta['url'] ?? url('/') }}">
    <meta property="twitter:title" content="{{ $meta['title'] ?? '諾摩浪浪' }}">
    <meta property="twitter:description" content="{{ $meta['description'] ?? '' }}">
    <meta property="twitter:image" content="{{ $meta['image'] ?? asset('images/og-image.jpg') }}">

    @if(isset($schema))
        @foreach($schema as $s)
            <script type="application/ld+json">
                                                        {!! json_encode($s, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
                                                    </script>
        @endforeach
    @endif
</head>

<body class="antialiased">
    <div id="app"></div>
    @vite(['resources/js/main.ts'])
</body>

</html>