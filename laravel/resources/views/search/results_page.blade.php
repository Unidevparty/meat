@foreach ($results as $result)
    @include('search.card', ['result' => $result])
@endforeach

@if ($results->nextPageUrl())
    <a href="{{ $results->nextPageUrl() }}" class="btn btn-reg btn-red wide load_more">загрузить еще</a>
@endif
