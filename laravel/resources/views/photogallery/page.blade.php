@foreach ($galleries as $gallery)
	@include('photogallery.thumb', ['photogallery' => $gallery])
@endforeach

@if ($more_link)
	<a href="{{ $more_link }}" class="btn load_more btn-reg btn-red wide">загрузить еще</a>
@endif
