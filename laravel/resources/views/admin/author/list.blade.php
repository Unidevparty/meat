@extends('layouts.admin')

@section('content')
    <form action="{{ route('author.index') }}" method="get">
    	<p>
    		<a href="{{ route('author.create') }}" class="btn btn-success">Создать автора</a>

            <span class="col-md-8 pull-right">
                <span class="col-md-10">
                    <input class="form-control search_by_name" placeholder="Поиск по названию" name="search" value="{{ $search }}">
                </span>
    			<span class="col-md-2">
                    <button type="submit" class="btn btn-primary"><span class="fa fa-search"></span></button>

    				<a href="{{ route('author.index') }}" class="btn btn-danger clear"><span class="fa fa-close"></span></a>
    			</span>
    		</span>
    	</p>
    </form>

	<hr>
	@if ($authors->count())
		<table class="table table-striped">
			<tbody>
				<tr>
					<th style="width: 10px">#</th>
					<th></th>
					<th>ФИО</th>
					<th>Должность</th>
					<th>Компания</th>
					<th>Дата изменения</th>
					<th>Дата создания</th>
					<th width="140"></th>
				</tr>
				@foreach ($authors as $author)
					<tr>
						<td class="align-middle">{{ $author->id }}</td>
						<td class="align-middle"><img src="{{ resize($author->photo, 45, 45) }}" class="img-circle"></td>
						<td class="align-middle">{{ $author->name }}</td>
						<td class="align-middle">{{ $author->post }}</td>
						<td class="align-middle">{{ $author->company->name }}</td>
						<td class="align-middle">{{ $author->updated_at->format('Y-m-d H:i:s') }}</td>
						<td class="align-middle">{{ $author->created_at->format('Y-m-d H:i:s') }}</td>
						<td class="align-middle">

							<a href="{{ route('author.edit' , $author->id) }}" class="btn btn-primary">
                                <span class="fa fa-pencil"></span>
                            </a>

                            {!! Form::open(['route' => ['author.destroy', $author->id], 'method' => 'DELETE', 'class' => 'inline-block']) !!}
                                <button type="submit" class="btn delete btn-danger">
                                    <span class="fa fa-close"></span>
                                </button>
                            {!! Form::close() !!}
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>

		{!! $authors->render() !!}
	@endif
@endsection


@section('scripts')
    <script src="/assets/admin_assets/plugins/autocomplete/jquery.autocomplete.min.js"></script>
	<script>
		// Подсказки для поиска
		$('.search_by_name').autocomplete({
		    serviceUrl: '{{ route('author.search') }}',
		    onSelect: function(suggestion) {
		        // При клике открываем соотв страницу
		        location.href = suggestion.data;
		    }
		});

	</script>
@endsection
