@extends('layouts.admin')

@section('content')
	<div class="box-body pad">
		<div class="box">
			<div class="box-body">
				{!! Form::open(['route' => 'settings.save', 'enctype' => 'multipart/form-data']) !!}

					@foreach ($settings as $param)
						<div class="form-group">
						   	<label for="{{ $param->key }}">{{ $param->name }}</label>
						   	@if ($param->key == 'bottom_scripts')
						   		{{ Form::textarea('data[' . $param->key . ']', $param->value, ['class' => 'form-control', 'id' => $param->key, 'rows' => 4]) }}
						   	@else
						   		{{ Form::text('data[' . $param->key . ']', $param->value, ['class' => 'form-control', 'id' => $param->key]) }}
						   	@endif
						</div>
					@endforeach

					<h3>RSS</h3>
					<p>
						<b>Новости:</b> https://meat-expert.ru/news/feed
						<br>
						<b>Статьи:</b> https://meat-expert.ru/articles/feed
						<br>
						<b>Интервью:</b> https://meat-expert.ru/interviews/feed
					</p>
					

					<p>
						<input type="submit" class="btn btn-success" value="Сохранить">
					</p>
			   {!! Form::close() !!}
			</div>
		</div>
	</div>
@endsection