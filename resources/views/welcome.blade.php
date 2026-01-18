<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="canonical" href="{{ $meta['url'] ?? url('/') }}">

    <title>{{ $meta['title'] ?? '諾摩浪浪' }}</title>
    <meta name="description" content="{{ $meta['description'] ?? '' }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
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