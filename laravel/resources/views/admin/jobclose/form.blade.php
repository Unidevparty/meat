@extends('layouts.admin')

@section('content')

	<div class="box-body pad">
		<div class="box">
			<div class="box-body">
				@if (!empty($job_close))
					{!! Form::model($job_close, ['route' => ['job_close.update', $job_close->id], 'method' => 'PATCH', 'enctype' => 'multipart/form-data']) !!}
			    @else
					{!! Form::open(['route' => 'job_close.store', 'enctype' => 'multipart/form-data']) !!}
			    @endif
					<div class="form-group{{ $errors->has('name') ? ' has-error' :'' }}">
					   <label for="name">Причина</label>
					   {{ Form::text('name', empty($job_close) ? null : $job_close->name, ['class' => 'form-control', 'id' => 'name']) }}
					</div>

					<p>
						<a href="{{ route('job_close.index') }}" class="btn btn-danger">Отмена</a>
						<input type="submit" class="btn btn-success" value="Сохранить">
					</p>
			   {!! Form::close() !!}
			</div>
		</div>
	</div>
@endsection
