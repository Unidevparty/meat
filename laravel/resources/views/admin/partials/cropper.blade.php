<div class="form-group {{ $name }}" style="min-width: {{ $width }}px;">
	<label for="image">Изображение</label>
	<input type="hidden" name="{{ $name }}" value="" class="image_input">

	@if (!empty($news) && !empty($news->image))
		<div class="image_crop_wrap">
			<img src="{{ $news->$name }}" alt="{{ $news->name }}" class="image_crop">
		</div>
		<p>
			<a href="#" class="crop_start btn btn-primary">Обрезать изображение</a>
			<a href="#" class="crop_reset btn btn-danger hidden2">Отменить</a>
		</p>
		<p>
			<b>Или загрузить файл</b>
			<input type="file" name="{{ $name }}_image_file" class="image_file_input" accept="image/x-png,image/gif,image/jpeg">
		</p>
	@else
		<input type="file" name="{{ $name }}_image_file" accept="image/x-png,image/gif,image/jpeg" class="image_file_input">
		<div class="image_crop_wrap hidden2">
			<img src="" alt="" class="image_crop">
		</div>
	@endif
</div>
@section('scripts')
	@parent
	<script type="text/javascript">
		$(function() {
			init_cropper($('.{{ $name }}'), {{ $width }}, {{ $height }});
		});
	</script>
@endsection
