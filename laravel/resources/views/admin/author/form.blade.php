@extends('layouts.admin')

@section('content')
	<div class="box-body pad">
		<div class="box">
			<div class="box-body">
				@if (!empty($author))
					{!! Form::model($author, ['route' => ['author.update', $author->id], 'method' => 'PATCH', 'enctype' => 'multipart/form-data']) !!}
			    @else
					{!! Form::open(['route' => 'author.store', 'enctype' => 'multipart/form-data']) !!}
			    @endif
					<div class="form-group{{ $errors->has('name') ? ' has-error' :'' }}">
					   <label for="name">ФИО</label>
					   {{ Form::text('name', empty($author) ? null : $author->name, ['class' => 'form-control', 'id' => 'name']) }}
					</div>


					<div class="form-group{{ $errors->has('post') ? ' has-error' :'' }}">
					   <label for="post">Должность</label>
					   {{ Form::text('post', empty($author) ? null : $author->post, ['class' => 'form-control', 'id' => 'post']) }}
					</div>



					<div class="input-group">
						<span class="input-group-btn">
						<a id="lfm" data-input="photo" data-preview="holder" class="btn btn-primary">
							<i class="fa fa-picture-o"></i> Выбрать фото автора
						</a>
						</span>
						<input id="photo" class="form-control" type="text" name="photo" value="{{ isset($author) ? $author->photo : '' }}">
					</div>
					<img src="{{ isset($author) ? $author->photo : '' }}" id="holder" style="margin-top:15px;max-height:100px;">

					<div class="form-group">
						<label>Компания</label>
						{{ Form::select('company_id', $companies, empty($author) ? null : $author->company->id, ['class' => 'form-control ', 'id' => 'company']) }}
					</div>



					<p>
						<a href="{{ route('author.index') }}" class="btn btn-danger">Отмена</a>
						<input type="submit" class="btn btn-success" value="Сохранить">
					</p>
			   {!! Form::close() !!}
			</div>
		</div>
	</div>
@endsection

@section('scripts')
	<script src="/vendor/laravel-filemanager/js/lfm.js"></script>
	<script type="text/javascript">
		$('#lfm').filemanager('image&wdir=/uploads/authors');
	</script>
@endsection
