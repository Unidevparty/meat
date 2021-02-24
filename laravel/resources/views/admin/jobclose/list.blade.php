@extends('layouts.admin')

@section('content')
	<p>
		<a href="{{ route('job_close.create') }}" class="btn btn-success">Создать причну закрытия вакансии</a>
	</p>
	<hr>

		<table class="table table-striped">
			<tbody>
				<tr>
					<th style="width: 10px">#</th>
					<th>Название</th>
					<th width="140"></th>
				</tr>
				@if ($jobCloses->count())
					@foreach ($jobCloses as $job_close)
						<tr>
							<td>{{ $job_close->id }}</td>
							<td>{{ $job_close->name }}</td>
							<td>
								<a href="{{ route('job_close.edit' , $job_close->id) }}" class="btn btn-primary">
	                                <span class="fa fa-pencil"></span>
	                            </a>

	                            {!! Form::open(['route' => ['job_close.destroy', $job_close->id], 'method' => 'DELETE', 'class' => 'inline-block']) !!}
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
		@if ($jobCloses->count())
			{!! $jobCloses->render() !!}
		@endif
@endsection
