@extends('layouts.admin')

@section('content')
	<p>
		<a href="{{ route('forum_on_main.create') }}" class="btn btn-success">Создать запись</a>
	</p>
	<hr>
	@if ($forums->count())
		<table class="table table-striped">
			<tbody>
				<tr>
					<th style="width: 10px">#</th>
					<th>Название</th>
					<th>ID</th>
					<th width="140"></th>
				</tr>
				@foreach ($forums as $forum)
					<tr>
						<td>{{ $forum->id }}</td>
						<td>{{ $forum->name }}</td>
						<td>{{ $forum->forum_id }}</td>
						<td>
							<a href="{{ route('forum_on_main.edit' , $forum->id) }}" class="btn btn-primary">
                                <span class="fa fa-pencil"></span>
                            </a>

                            {!! Form::open(['route' => ['forum_on_main.destroy', $forum->id], 'method' => 'DELETE', 'class' => 'inline-block']) !!}
                                <button type="submit" class="btn delete btn-danger">
                                    <span class="fa fa-close"></span>
                                </button>
                            {!! Form::close() !!}
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>

		{!! $forums->render() !!}
	@endif
@endsection