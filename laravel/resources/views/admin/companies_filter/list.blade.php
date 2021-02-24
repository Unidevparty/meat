@extends('layouts.admin')

@section('content')
	<p>
		<a href="{{ route('companies_filter.create') }}" class="btn btn-success">Создать фильтр компаний</a>
	</p>
	<hr>

		<table class="table table-striped">
			<tbody>
				<tr>
					<th style="width: 10px">#</th>
					<th>Название</th>
					<th>Тип</th>
					<th width="140"></th>
				</tr>
				@if ($companies_filters->count())
					@foreach ($companies_filters as $companies_filter)
						<tr>
							<td>{{ $companies_filter->id }}</td>
							<td>{{ $companies_filter->name }}</td>
							<td>{{ $companies_filter->type == "top" ? 'Топ запросов' : 'Блок с цифрами' }}</td>
							<td>
								<a href="{{ route('companies_filter.edit' , $companies_filter->id) }}" class="btn btn-primary">
	                                <span class="fa fa-pencil"></span>
	                            </a>

	                            {!! Form::open(['route' => ['companies_filter.destroy', $companies_filter->id], 'method' => 'DELETE', 'class' => 'inline-block']) !!}
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
		@if ($companies_filters->count())
			{!! $companies_filters->render() !!}
		@endif
@endsection
