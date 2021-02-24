@extends('layouts.admin')

@section('content')
	<div class="box-body pad">
		<div class="box">
			<div class="box-body">
				@if (!empty($subscriber))
					{!! Form::model($subscriber, ['route' => ['subscriber.update', $subscriber->id], 'method' => 'PATCH']) !!}
			    @else
					{!! Form::open(['route' => 'subscriber.store', 'enctype' => 'multipart/form-data']) !!}
			    @endif
					<div class="form-group{{ $errors->has('email') ? ' has-error' :'' }}">
					   <label for="email">Email</label>
					   {{ Form::text('email', empty($subscriber) ? null : $subscriber->email, ['class' => 'form-control', 'id' => 'email']) }}
					</div>

					<p>
						<a href="{{ route('subscriber.index') }}" class="btn btn-danger">Отмена</a>
						<input type="submit" class="btn btn-success" value="Сохранить">
					</p>
			   {!! Form::close() !!}
			</div>
		</div>
	</div>
@endsection