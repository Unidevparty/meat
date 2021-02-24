@extends('layouts.admin')

@section('content')

    <p>
		<a href="{{ route('company.create') }}" class="btn btn-success">Создать компанию</a>

		<span class="col-md-8 pull-right">
			<span class="col-md-2">
				<input class="form-control date_mask" name="start" placeholder="YYYY-MM-DD" value="{{ $start }}">
			</span>
			<span class="col-md-2">
				<input class="form-control date_mask" name="end" placeholder="YYYY-MM-DD" value="{{ $end }}">
			</span>
			<span class="col-md-2">
				<a href="#" class="btn btn-primary search_by_date"><span class="fa fa-search"></span></a>
				<a href="#" class="btn btn-danger clear"><span class="fa fa-close"></span></a>
			</span>
			<span class="col-md-6">
				<input class="form-control search_by_name" placeholder="Поиск по названию">
			</span>
		</span>
	</p>
	<hr>

	<table class="table table-striped">
		<tbody>
			<tr>
				<th style="width: 10px">#</th>
				<th>Название</th>
				<th width="140"></th>
			</tr>
			@if ($companies->count())
				@foreach ($companies as $company)
					<tr>
						<td>{{ $company->id }}</td>
						<td>{{ $company->name }}</td>
						<td>
                            <a href="{{ route('company.show' , $company->alias) }}" target="_blank" class="btn btn-primary">
                                <span class="fa fa-eye"></span>
                            </a>

							<a href="{{ route('company.edit' , $company->id) }}" class="btn btn-primary">
                                <span class="fa fa-pencil"></span>
                            </a>

                            {!! Form::open(['route' => ['company.destroy', $company->id], 'method' => 'DELETE', 'class' => 'inline-block']) !!}
                                <button type="submit" class="btn delete btn-danger">
                                    <span class="fa fa-close"></span>
                                </button>
                            {!! Form::close() !!}
						</td>
					</tr>
				@endforeach
			@endif
		</tbody>
	</table>
	@if ($companies->count())
		{!! $companies->render() !!}
	@endif
@endsection


@section('scripts')
    <script src="/assets/admin_assets/plugins/autocomplete/jquery.autocomplete.min.js"></script>
	<script>
		// Подсказки для поиска
		$('.search_by_name').autocomplete({
		    serviceUrl: '{{ route('company.search') }}',
		    onSelect: function(suggestion) {
		        // При клике открываем соотв страницу
		        location.href = suggestion.data;
		    }
		});

		$('.search_by_date').click(function() {
			var start = $('[name="start"]').val();
			var end = $('[name="end"]').val();

			location.href = location.href.split('?')[0] + '?start=' + start + '&end=' + end;

			return false;
		});

		$('.clear').click(function() {
			location.href = location.href.split('?')[0];

			return false;
		});
	</script>
@endsection
