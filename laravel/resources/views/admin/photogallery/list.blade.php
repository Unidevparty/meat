@extends('layouts.admin')

@section('content')
	<p>
		<a href="{{ route('photogallery.create') }}" class="btn btn-success">Создать фотогалерею</a>
		<span class="col-md-6 pull-right">
			<span class="col-md-8">
				<input class="form-control search_by_name" placeholder="Поиск по названию">
			</span>
			<span class="col-md-4">
				<a href="#" class="btn btn-primary search_by_date"><span class="fa fa-search"></span></a>
				<a href="#" class="btn btn-danger clear"><span class="fa fa-close"></span></a>
			</span>
		</span>
	</p>
	<hr>
	@if ($photogalleries->count())
		<table class="table table-striped table-middle">
			<tbody>
				<tr>
					<th style="width: 10px">#</th>
					<th>Фото</th>
					<th>Название</th>
					<th>Опубликовано</th>
					<th>Дата публикации</th>
					<th>Дата изменения</th>
					<th>Дата создания</th>
					<th width="140"></th>
					<th width="80">Кол-во</th>
					<th width="40">Просмотры</th>
				</tr>
				@foreach ($photogalleries as $gallery)
					<tr>
						<td>{{ $gallery->id }}</td>
						<td><img src="{{ resize($gallery->image, 45, 45) }}" class="img-circle"></td>
						<td>{{ $gallery->name }}</td>
						<td class="text-center"><span class="fa {{ $gallery->published ? 'fa-check' : 'fa-close' }}"></span></td>
						<td>{{ $gallery->published_at ? $gallery->published_at->format('Y-m-d H:i:s') : '' }}</td>
						<td>{{ $gallery->updated_at->format('Y-m-d H:i:s') }}</td>
						<td>{{ $gallery->created_at->format('Y-m-d H:i:s') }}</td>
						<td>
							<a href="{{ route('photogallery.show' , $gallery->alias) }}" target="_blank" class="btn btn-primary">
                                <span class="fa fa-eye"></span>
                            </a>

							<a href="{{ route('photogallery.edit' , $gallery->id) }}" class="btn btn-primary">
                                <span class="fa fa-pencil"></span>
                            </a>

                            {!! Form::open(['route' => ['photogallery.destroy', $gallery->id], 'method' => 'DELETE', 'class' => 'inline-block']) !!}
                                <button type="submit" class="btn delete btn-danger">
                                    <span class="fa fa-close"></span>
                                </button>
                            {!! Form::close() !!}
						</td>
						<td class="text-center"><span class="badge bg-light-blue">{{ count($gallery->photos) }}</span></td>
						<td class="text-center"><span class="badge bg-light-blue">{{ $gallery->views }}</span></td>
					</tr>
				@endforeach
			</tbody>
		</table>

		{!! $photogalleries->render() !!}
	@endif
@endsection

@section('scripts')
    <script src="/assets/admin_assets/plugins/autocomplete/jquery.autocomplete.min.js"></script>
	<script>
		// Подсказки для поиска
		$('.search_by_name').autocomplete({
		    serviceUrl: '{{ route('photogallery.search') }}',
		    onSelect: function(suggestion) {
		        // При клике открываем соотв страницу
		        location.href = suggestion.data;
		    }
		});

		$('.clear').click(function() {
			location.href = location.href.split('?')[0];

            $('.search_by_name').val('');

			return false;
		});
	</script>
@endsection
