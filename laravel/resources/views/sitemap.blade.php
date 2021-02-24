<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{date('Y-m-d')}}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1</priority>
    </url>
    <url>
        <loc>{{ url(route('news.list')) }}</loc>
        <lastmod>{{date('Y-m-d')}}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1</priority>
    </url>
    <url>
        <loc>{{ url(route('articles.list')) }}</loc>
        <lastmod>{{date('Y-m-d')}}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1</priority>
    </url>
    <url>
        <loc>{{ url(route('interviews.list')) }}</loc>
        <lastmod>{{date('Y-m-d')}}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1</priority>
    </url>


    @foreach($news as $page)
        <url>
            <loc>{{ url(route('news.show', $page->alias)) }}</loc>
            <lastmod>{{$page->published_at->format('Y-m-d')}}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach

    @foreach($articles as $page)
        <url>
            <loc>{{ url(route('articles.show', $page->alias)) }}</loc>
            <lastmod>{{$page->published_at->format('Y-m-d')}}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach

    @foreach($interviews as $page)
        <url>
            <loc>{{ url(route('interviews.show', $page->alias)) }}</loc>
            <lastmod>{{$page->published_at->format('Y-m-d')}}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach

    @foreach($pages as $page)
        <url>
            <loc>{{ url($page->url) }}</loc>
            <lastmod>{{$page->published_at->format('Y-m-d')}}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach
</urlset>
