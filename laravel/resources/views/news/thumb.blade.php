<div class="article-thumb thumb-element">
	<a href="{{ route('news.show' , $new->alias) }}">
		<img src="{{ $new->preview }}" alt="">
		<div class="h">{{ $new->name }}</div>
		<span class="date">
			{{ LocalizedCarbon::instance($new->published_at)->formatLocalized('%d %f ‘%y') }}
			@if (hasPermissions('news'))
				<small>{{ $new->published_at->format('h:i:s') }}</small>
			@endif
		</span>
		<span class="stats">
			<span class="stats-unit">
				<i class="icon icon-views"></i>
				{{ $new->views or 0 }}
			</span>
		
			<span class="stats-unit">                                    
				@if ($new->comments_count == 0)
										
				@else
					<i class="icon icon-comments"></i>
						{{ $new->comments_count }}
				@endif
            </span>
			
			
		</span>
	</a>
</div>
