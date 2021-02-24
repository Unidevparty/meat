<div class="interview-card search-card">
    <header>
        <div class="type">новости</div>
        <span class="date">
            {{ LocalizedCarbon::instance($searchable->published_at)->formatLocalized('%d %f ‘%y') }}
        </span>
    </header>
    <figure>
        <a href="{{ route('news.show', $searchable->alias) }}"><img src="{{ resize($searchable->preview, 194, 145) }}" alt="{{ $searchable->name }}"></a>
        <footer>
            <span class="stats">
                <span class="stats-unit">
                    <i class="icon icon-views"></i>
                    {{ $searchable->views or 0 }}
                </span>
                <span class="stats-unit">
                    <i class="icon icon-comments"></i>
                    {{ $searchable->comments_count or 0 }}
                </span>
            </span>
        </footer>
    </figure>
    <div class="interview-card-description">
        <div class="tags">
            @foreach ($searchable->tags as $tag)
                <small class="tag"><a href="{{ route('articles.tag', $tag->alias) }}">{{ $tag->name }}</a></small>
            @endforeach
        </div>


        <h3><a href="{{ route('news.show', $searchable->alias) }}">{{ $searchable->name }}</a></h3>
        <p><a href="{{ route('news.show', $searchable->alias) }}">{{ $searchable->introtext }}</a></p>
    </div>
</div>
