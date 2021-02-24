@extends('layouts.admin')

@section('content')
	<link rel="stylesheet" href="/assets/admin_assets/plugins/cropperjs/cropper.min.css">
	<div class="box-body pad">
		<div class="box">
			<div class="box-body">
				{!! Form::open([['route' => 'pages.update', 'file' => $file], 'enctype' => 'multipart/form-data']) !!}
					<input type="hidden" name="path" value="{{ $path }}">
					<div class="form-group">
					   <label for="file_content">Контент</label>
					   {{ Form::textarea('file_content', empty($file_content) ? null : $file_content, ['class' => 'form-control', 'id' => 'file_content', 'rows' => 30]) }}
					</div>

					<p>
						<a href="{{ route('pages.index') }}" class="btn btn-danger">Отмена</a>
						<input type="submit" class="btn btn-success" value="Сохранить">
					</p>
			   {!! Form::close() !!}
			</div>
		</div>
	</div>
@endsection
