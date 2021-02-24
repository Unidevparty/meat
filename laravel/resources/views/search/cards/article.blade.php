<div class="interview-card search-card">
    <header>
        <div class="type">Статьи</div>
        <span class="date">
            {{ LocalizedCarbon::instance($searchable->published_at)->formatLocalized('%d %f ‘%y') }}
        </span>
    </header>
    <figure>
        <a href="{{ route('articles.show', $searchable->alias) }}"><img src="{{ resize($searchable->preview, 194, 145) }}" alt="{{ $searchable->name }}"></a>
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


        <h3><a href="{{ route('articles.show', $searchable->alias) }}">{{ $searchable->name }}</a></h3>
        <p><a href="{{ route('articles.show', $searchable->alias) }}">{{ $searchable->introtext }}</a></p>

        @if ($searchable->authors()->count())
            <div class="authors_in_article">
                @foreach ($searchable->authors as $author)
                    <div class="author">
                        <div class="author-in">
                            <img src="{{ resize($author->photo, 91, 91) }}" alt="{{ $author->name }}" width="65">
                            <div class="data">
                                <div class="name">{{ $author->name }}</div>
                                <div class="company">{{ $author->company->name }}</div>
                                <div class="post">{{ $author->post }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif


    </div>
</div>
