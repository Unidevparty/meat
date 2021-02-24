@extends('layouts.admin')

@section('content')
	<p>
		<a href="{{ route('page.create') }}" class="btn btn-success">Создать страницу</a>
	</p>
	<hr>
	
		<table class="table table-striped">
			<tbody>
				<tr>
					<th style="width: 10px">#</th>
					<th>Название</th>
					<th>Опубликовано</th>
					<th>Дата публикации</th>
					<th>Дата изменения</th>
					<th>Дата создания</th>
					<th width="140"></th>
					<th width="40">Просмотры</th>
				</tr>
				@if ($pages->count())
					@foreach ($pages as $page)
						<tr>
							<td>{{ $page->id }}</td>
							<td>{{ $page->name }}</td>
							<td class="text-center"><span class="fa {{ $page->published ? 'fa-check' : 'fa-close' }}"></span></td>
							<td>{{ $page->published_at ? $page->published_at->format('Y-m-d H:i:s') : '' }}</td>
							<td>{{ $page->updated_at->format('Y-m-d H:i:s') }}</td>
							<td>{{ $page->created_at->format('Y-m-d H:i:s') }}</td>
							<td>
								<a href="{{ $page->url }}" target="_blank" class="btn btn-primary">
	                                <span class="fa fa-eye"></span>
	                            </a>
								<a href="{{ route('page.edit' , $page->id) }}" class="btn btn-primary">
	                                <span class="fa fa-pencil"></span>
	                            </a>

	                            {!! Form::open(['route' => ['page.destroy', $page->id], 'method' => 'DELETE', 'class' => 'inline-block']) !!}
	                                <button type="submit" class="btn delete btn-danger">
	                                    <span class="fa fa-close"></span>
	                                </button>
	                            {!! Form::close() !!}
							</td>
							<td class="text-center"><span class="badge bg-light-blue">{{ $page->views }}</span></td>
						</tr>
					@endforeach
				@endif
			</tbody>
		</table>
		@if ($pages->count())
			{!! $pages->render() !!}
		@endif
@endsection