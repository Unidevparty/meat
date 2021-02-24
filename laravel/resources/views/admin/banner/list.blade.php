@extends('layouts.admin')

@section('content')
	<p>
		<a href="{{ route('banner.create') }}" class="btn btn-success">Создать баннер</a>
	</p>
	<hr>
	
		<table class="table table-striped">
			<tbody>
				<tr>
					<th style="width: 10px">#</th>
					<th>Название</th>
					<th>
						<a href="?bydefault={{ !empty($bydefault) ? 0 : 1 }}">
							<span class="fa {{ !empty($bydefault) ? 'fa-check' : 'fa-close' }}"></span>
							По умолчанию
						</a>
					</th>
					<th>Опубликовано</th>
					<th>Дата публикации</th>
					<th>Дата окончания</th>
					<th>Дата изменения</th>
					<th>Дата создания</th>
					<th>
						Позиция <br>
						<form action="" class="positions" method="get">
							<select name="position">
								<option value="">Все</option>
								@foreach (App\Banner::POSITIONS as $key => $pos)
									<option value="{{ $key }}" {{ $position == $key ? 'selected' : '' }}>{{ $pos }}</option>
								@endforeach
							</select>
						</form>
					</th>
					<th width="140"></th>
					<th width="40">Просмотры</th>
					<th width="40">Переходы</th>
				</tr>
				@if ($banners->count())
					@foreach ($banners as $banner)
						<tr>
							<td>{{ $banner->id }}</td>
							<td>{{ $banner->name }}</td>
							<td class="text-center"><span class="fa {{ $banner->bydefault ? 'fa-check' : 'fa-close' }}"></span></td>
							<td class="text-center"><span class="fa {{ $banner->published ? 'fa-check' : 'fa-close' }}"></span></td>
							<td>{{ $banner->start_date ? $banner->start_date->format('Y-m-d H:i:s') : '' }}</td>
							<td>{{ $banner->end_date ? $banner->end_date->format('Y-m-d H:i:s') : '' }}</td>
							<td>{{ $banner->updated_at->format('Y-m-d H:i:s') }}</td>
							<td>{{ $banner->created_at->format('Y-m-d H:i:s') }}</td>
							<td>{{ $banner->position_name }}</td>
							<td>
								<a href="{{ route('banner.edit' , $banner->id) }}" class="btn btn-primary">
	                                <span class="fa fa-pencil"></span>
	                            </a>

	                            {!! Form::open(['route' => ['banner.destroy', $banner->id], 'method' => 'DELETE', 'class' => 'inline-block']) !!}
	                                <button type="submit" class="btn delete btn-danger">
	                                    <span class="fa fa-close"></span>
	                                </button>
	                            {!! Form::close() !!}
							</td>
							<td class="text-center"><span class="badge bg-light-blue">{{ $banner->views }}</span></td>
							<td class="text-center"><span class="badge bg-light-blue">{{ $banner->clicks }}</span></td>
						</tr>
					@endforeach
				@endif
			</tbody>
		</table>
		@if ($banners->count())
			{!! $banners->render() !!}
		@endif
@endsection
@section('scripts')
	<script>
		$('[name="position"]').change(function() {
			$('form.positions').submit();
		});
	</script>
@endsection
