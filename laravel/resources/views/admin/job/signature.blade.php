@extends('layouts.admin')

@section('content')
	<div class="box-body pad">
		<div class="box">
			<div class="box-body">
				{!! Form::open(['route' => 'job.store_signature', 'enctype' => 'multipart/form-data']) !!}

					<div class="form-group">
					   <label for="signature">Подпись</label>
					   {{ Form::textarea('signature', $signature ? $signature : null, ['class' => 'form-control', 'id' => 'signature']) }}
					</div>

					<div class="input-group">
					   <span class="input-group-btn">
   						<a id="lfm" data-input="example" data-preview="holder" class="btn btn-primary">
   							<i class="fa fa-file-o"></i> Пример заполнению резюме
   						</a>
   						</span>
   						<input id="example" class="form-control" type="text" name="example" value="{{ isset($example) ? $example : '' }}">
					</div>

					<br>


					<p>
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
		$(function () {
			$('#lfm').filemanager('file');

			CKEDITOR.replace('signature', {
				filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
				filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token=',
				filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
				filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token=',
				height: '400px',
			});
		});
	</script>
@endsection
