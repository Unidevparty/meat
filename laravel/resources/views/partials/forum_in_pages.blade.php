<div class="content-section">
	<div class="section-header">
		<div class="h">Форум</div>
		<a href="/forums/" class="btn btn-reg btn-red">перейти в форум</a>
	</div>
	<?php
		$topics = \App\Forum::topics();
	?>
	<div class="forum-thumbs">
		@foreach ($topics as $topic)
			<div class="forum-thumb">
				<div class="forum-thumb-category">
					<div class="forum-category-ph bg-red">
						<i class="icon icon-forum-unity"></i>
					</div>
				</div>
				<div class="forum-thumb-description">
					<div class="vfix">
						<a href="{{ $topic['url'] }}" class="h">
					{{ $topic['title'] }}
				</a>
				<p>{{ cut_text(strip_tags($topic['firstPost']['content']), 100) }}</p>
					</div>
				</div>
				<div class="forum-thumb-msgs">
					<div class="vfix">
						<i class="icon icon-comments"></i>
						{{ $topic['posts'] }}
					</div>
				</div>
				<div class="forum-thumb-author">
					<div class="vfix">
						<div class="author-module">
							<div class="author-userpic">
								<img src="{{ resize($topic['firstPost']['author']['photoUrl'], 46, 46) }}" width="46" alt="{{ $topic['firstPost']['author']['name'] }}">
							</div>
							<div class="author-details">
								<a href="{{ $topic['firstPost']['author']['profileUrl'] }}" class="author-name">{{ $topic['firstPost']['author']['name'] }}</a>
								<span class="author-date">{{ LocalizedCarbon::instance(Carbon\Carbon::parse($topic['firstPost']['date']))->formatLocalized('%d %f ‘%y') }}</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		@endforeach
	</div>
</div>