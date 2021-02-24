@foreach ($companies as $company)
	@include('company.thumb', ['company' => $company])
@endforeach

@if ($more_link)
	<a href="{{ $more_link }}" class="btn load_more btn-reg btn-red wide">загрузить еще</a>
@endif
