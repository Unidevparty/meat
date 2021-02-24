<div class="mc_{{ $name }}">
	<label for="image">Изображение</label>

	<input type="hidden" name="source" value="{{ !empty($object) ? $object->{$source_image} : '' }}">
	<input type="file" name="{{ $source_image }}" accept="image/x-png,image/gif,image/jpeg" class="image_file_input">

	@foreach ($cropper_images as $cropper)
		<div class="form-group {{ $cropper['name'] }}" data-name="{{ $cropper['name'] }}" data-width="{{ $cropper['width'] }}" data-height="{{ $cropper['height'] }}">


			<input type="hidden" name="{{ $cropper['name'] }}" value="" class="image_input" disabled>

			@if (!empty($object) && !empty($object->{$cropper['name']}))
				<div class="image_crop_wrap">
					<img src="{{ $object->{$cropper['name']} }}" alt="{{ $object->name }}" class="image_crop" style="max-width: 100%">
				</div>
				<p>
					<a href="#" class="crop_start btn btn-primary">Обрезать изображение</a>
					<a href="#" class="crop_reset btn btn-danger hidden2">Отменить</a>
				</p>
			@else
				<div class="image_crop_wrap hidden2">
					<img src="" alt="" class="image_crop">
				</div>
			@endif
		</div>
	@endforeach
</div>

@section('scripts')
	@parent
	<script type="text/javascript">
		$(function() {
			init_multiple_cropper($('.mc_{{ $name }}'));
		});
	</script>
@endsection
