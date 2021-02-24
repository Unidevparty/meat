<div class="article-thumb thumb-element">
    <a href="{{ route('photogallery.show', $photogallery->alias) }}">
        <img src="{{ $photogallery->category_image }}" alt="{{ $photogallery->name }}">
        <div class="h">{{ $photogallery->name }}</div>
        <span class="date">
            {{ LocalizedCarbon::instance($photogallery->published_at)->formatLocalized('%d %f ‘%y') }}
        </span>
        <span class="stats">
			<span class="stats-unit">
                <i class="icon icon-camera"></i>
                {{ count($photogallery->photos) }}
            </span>
			<span class="stats-unit">
				<i class="icon icon-views"></i>
				{{ $photogallery->views or 0 }}
			</span>
            <span class="stats-unit">                                    
				@if ($photogallery->comments_count == 0)
										
				@else
					<i class="icon icon-comments"></i>
						{{ $photogallery->comments_count }}
				@endif
            </span>
			
        </span>
    </a>
</div>
