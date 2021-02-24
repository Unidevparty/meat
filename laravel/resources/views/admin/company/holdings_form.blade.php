@extends('layouts.admin')

@section('content')
	<div class="box-body pad">
		<div class="box">
			<div class="box-body">
				@if (!empty($holding))
					{!! Form::model($holding, ['route' => ['company_holding.update', $holding->id], 'method' => 'PATCH', 'enctype' => 'multipart/form-data']) !!}
			    @else
					{!! Form::open(['route' => 'company_holding.store', 'enctype' => 'multipart/form-data']) !!}
			    @endif
					<div class="form-group{{ $errors->has('name') ? ' has-error' :'' }}">
					   <label for="name">Название</label>
					   {{ Form::text('name', $holding->name?: '', ['class' => 'form-control', 'id' => 'name']) }}
					</div>


					<p>
						<a href="{{ route('company_holding.index') }}" class="btn btn-danger">Отмена</a>
						<input type="submit" class="btn btn-success" value="Сохранить">
					</p>
			   {!! Form::close() !!}
			</div>
		</div>
	</div>
@endsection
