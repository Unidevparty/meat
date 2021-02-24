<div class="article-thumb thumb-element">
	<a href="{{ route('articles.show', $article->alias) }}">
		@if (isset($size) && $size == 'big')
			<img src="{{ $article->preview_big }}" alt="{{ $article->name }}">
		@else
			<img src="{{ $article->preview }}" alt="{{ $article->name }}">
		@endif
		<div class="h">{{ $article->name }}</div>
		<span class="date">
			{{ LocalizedCarbon::instance($article->published_at)->formatLocalized('%d %f ‘%y') }}
		</span>
		<span class="stats">
			<span class="stats-unit">
				<i class="icon icon-views"></i>
				{{ $article->views or 0 }}
			</span>
		
			<span class="stats-unit">                                    
				@if ($article->comments_count == 0)
										
				@else
					<i class="icon icon-comments"></i>
						{{ $article->comments_count }}
				@endif
            </span>
		</span>
	</a>
</div>
