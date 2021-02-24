@foreach ($jobs as $job)
	@include('job.thumb', ['job' => $job])
@endforeach

@if ($next_page)
	<a href="{{ $more_url }}" class="btn load_more btn-reg btn-red wide">загрузить еще</a>
@endif
