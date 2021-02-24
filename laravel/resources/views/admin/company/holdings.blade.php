@extends('layouts.admin')

@section('content')
	<p>
		<a href="{{ route('company_holding.create') }}" class="btn btn-success">Создать холдинг</a>
	</p>
	<hr>

		<table class="table table-striped">
			<tbody>
				<tr>
					<th style="width: 10px">#</th>
					<th>Название</th>
					<th width="140"></th>
				</tr>
				@if ($holdings->count())
					@foreach ($holdings as $holding)
						<tr>
							<td>{{ $holding->id }}</td>
							<td>{{ $holding->name }}</td>
							<td>
								<a href="{{ route('company_holding.edit' , $holding->id) }}" class="btn btn-primary">
	                                <span class="fa fa-pencil"></span>
	                            </a>

	                            {!! Form::open(['route' => ['company_holding.destroy', $holding->id], 'method' => 'DELETE', 'class' => 'inline-block']) !!}
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
		@if ($holdings->count())
			{!! $holdings->render() !!}
		@endif
@endsection
