@foreach ($articles as $article)
	@include('articles.thumb', ['article' => $article])
@endforeach

@if ($next_page)
	<a href="{{ route('articles.more') }}?page={{ $next_page }}&author={{ $selected_author }}&company={{ $selected_company }}&search={{ $search }}&tag_alias={{ $tag_alias }}" class="btn load_more btn-reg btn-red wide">загрузить еще</a>
@endif