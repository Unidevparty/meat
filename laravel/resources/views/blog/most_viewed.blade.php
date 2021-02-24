<div class="content-section">
	<div class="wbox">
		<div class="section-header">
			<div class="h">Блоги — Читаемое</div>
			<a href="/forums/blogs/" class="btn btn-reg btn-red">все блоги</a>
		</div>
		<div class="blog-thumbs">
			@foreach (App\Blog::posts(5) as $post)
				<div class="blog-thumb">
					<div class="h"><a href="{{ $post['url'] }}">{{ $post['title'] }}</a></div>
					<p>{{ cut_text(strip_tags($post['entry']), 150) }}</p>
					<footer>
						<div class="author-details">
							<figure><img src="{{ resize($post['author']['photoUrl'], 46, 46) }}" alt="{{ $post['author']['name'] }}"></figure>
							<a href="{{ $post['author']['profileUrl'] }}" class="author">{{ $post['author']['name'] }}</a>
							<span class="date">{{ LocalizedCarbon::instance(Carbon\Carbon::parse($post['date']))->formatLocalized('%d %f ‘%y') }}</span>
						</div>
						<span class="stats">
							<span class="stats-unit">
								<i class="icon icon-views"></i>
								{{ $post['views'] }}
							</span>
							<span class="stats-unit">
								<i class="icon icon-comments"></i>
								{{ $post['comments'] }}
							</span>
						</span>
					</footer>
				</div>
			@endforeach
		</div>
	</div>
</div>