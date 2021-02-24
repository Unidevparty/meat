<div class="article-thumb article-thumb--forum thumb-element">
	<a href="{{ $forum['url'] }}">
		<img src="{{ $forum['image'] }}" alt="{{ $forum['title'] }}">
		<div class="h">{{ $forum['title'] }} <span class="author">{{ $forum['firstPost']['author']['name'] }}</span></div>
		<span class="forum-label">ФОРУМ</span>
	</a>
</div>