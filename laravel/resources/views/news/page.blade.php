@foreach ($news as $new)
	@include('news.thumb', ['new' => $new])
@endforeach

@if ($next_page)
	<a href="{{ route('news.more') }}?page={{ $next_page }}&tag_alias={{ $tag_alias }}" class="btn load_more btn-reg btn-red wide">загрузить еще</a>
@endif