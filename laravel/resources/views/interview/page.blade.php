@foreach ($interviews as $interview)
	@include('interview.thumb', ['interview' => $interview])
@endforeach

@if ($next_page)
	<a href="{{ $url }}" class="btn load_more btn-reg btn-red wide">загрузить еще</a>
@endif