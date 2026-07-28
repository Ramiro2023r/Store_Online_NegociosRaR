<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach($static as $page)
        <url>
            <loc>{{ $page['loc'] }}</loc>
            <priority>{{ $page['priority'] }}</priority>
        </url>
    @endforeach
    @foreach($categories as $cat)
        <url>
            <loc>{{ route('products.index', ['category' => $cat->slug]) }}</loc>
            <priority>0.7</priority>
            @if($cat->updated_at)<lastmod>{{ $cat->updated_at->toW3cString() }}</lastmod>@endif
        </url>
    @endforeach
    @foreach($products as $product)
        <url>
            <loc>{{ route('products.show', $product) }}</loc>
            <priority>0.8</priority>
            @if($product->updated_at)<lastmod>{{ $product->updated_at->toW3cString() }}</lastmod>@endif
        </url>
    @endforeach
</urlset>
