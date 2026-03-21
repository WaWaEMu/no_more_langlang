{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    <!-- Static Pages -->
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ $lastmod }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ url('/about') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc>{{ url('/adopt') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>

    <!-- Dynamic Pet Pages -->
    @foreach ($pets as $pet)
        <url>
            <loc>{{ url('/adopt/' . ($pet->slug ?: $pet->id)) }}</loc>
            <lastmod>{{ $pet->updated_at->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.6</priority>
            @foreach ($pet->images as $image)
                <image:image>
                    <image:loc>{{ asset('storage/' . $image->path) }}</image:loc>
                    <image:title>{{ $pet->title }}</image:title>
                    @if ($pet->detail && $pet->detail->adoption_description)
                        <image:caption>{{ Str::limit(strip_tags($pet->detail->adoption_description), 150) }}</image:caption>
                    @endif
                </image:image>
            @endforeach
        </url>
    @endforeach
</urlset>
