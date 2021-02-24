<div class="section-h">Еще новости</div>
<div class="news-brief-list">
	@foreach (\App\News::published()->take(5)->get() as $new)
		<div class="news-brief-thumb">
			<div class="h"><a href="{{ route('news.show', $new->alias) }}">{{ $new->name }}</a></div>
			<footer>
				<span class="date">
					{{ LocalizedCarbon::instance($new->published_at)->formatLocalized('%d %f ‘%y') }}
				</span>
				<span class="stats">
					
					<span class="stats-unit">                                    
					@if ($new->comments_count == 0)
										
					@else
						<i class="icon icon-comments"></i>
						{{ $new->comments_count }}
					@endif
					</span>
					
					<span class="stats-unit">
						<i class="icon icon-views"></i>
						{{ $new->views or 0 }}
					</span>
					
				</span>
			</footer>
		</div>
	@endforeach
</div>
