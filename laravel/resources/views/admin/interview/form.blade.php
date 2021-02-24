@extends('layouts.admin')

@section('content')
	<link rel="stylesheet" href="/assets/admin_assets/plugins/cropperjs/cropper.min.css">
	<div class="box-body pad">
		<div class="box">
			<div class="box-body">
				@if (!empty($interview))
					{!! Form::model($interview, ['route' => ['interview.update', $interview->id], 'method' => 'PATCH', 'enctype' => 'multipart/form-data']) !!}
			    @else
					{!! Form::open(['route' => 'interview.store', 'enctype' => 'multipart/form-data']) !!}
			    @endif
					<div class="form-group{{ $errors->has('fio') ? ' has-error' :'' }}">
					   <label for="fio">ФИО</label>
					   {{ Form::text('fio', empty($interview) ? null : $interview->fio, ['class' => 'form-control', 'id' => 'fio']) }}
					</div>

					<div class="form-group{{ $errors->has('post') ? ' has-error' :'' }}">
					   <label for="post">Должность</label>
					   {{ Form::text('post', empty($interview) ? null : $interview->post, ['class' => 'form-control', 'id' => 'post']) }}
					</div>

					<div class="form-group{{ $errors->has('quote') ? ' has-error' :'' }}">
					   <label for="quote">Цитата</label>
					   {{ Form::textarea('quote', empty($interview) ? null : $interview->quote, ['class' => 'form-control', 'id' => 'quote', 'rows' => 3]) }}
					</div>

					<div class="form-group">
						<label>Компания</label>
						{{ Form::select('company_id', $companies, empty($interview) ? null : $interview->company()->id, ['class' => 'form-control select-custom', 'id' => 'company']) }}
					</div>

					<div class="form-group">
					   <label for="text">Аннотация</label>
					   {{ Form::textarea('introtext', empty($interview) ? null : $interview->introtext, ['class' => 'form-control', 'rows' => 4]) }}
					</div>

					<div class="form-group">
					   <label for="text">Текст интервью</label>
					   {{ Form::textarea('text', empty($interview) ? null : $interview->text, ['class' => 'form-control', 'id' => 'text', 'rows' => 2]) }}
					</div>


					@include('admin.partials.cropper_multiple', [
						'object' => !empty($interview) ? $interview : null,
						'name' => 'interview',
						'source_image' => 'source_image',
						'cropper_images' => [
							['name' => 'main_image', 'width' => 228*2, 'height' => 228*2],
							['name' => 'preview', 'width' => 184*2, 'height' => 184*2],
						]
					])

					@if ($interview && $interview->source_image)
						<a href="{{ route('interview.delete_images', $interview->id) }}" class="btn btn-danger confirm">Удалить картинки</a>
					@endif

					{{-- @include('admin.partials.cropper', ['name' => 'image', 'width' => 940, 'height' => 496])
					@include('admin.partials.cropper', ['name' => 'preview', 'width' => 296, 'height' => 227])
					@include('admin.partials.cropper', ['name' => 'on_main', 'width' => 138, 'height' => 90]) --}}


					<div class="form-group">
					   <label for="title">Title <small>(<span class="l">0</span> / 70)</small></label>
					   {{ Form::text('title', empty($interview) ? null : $interview->title, ['class' => 'form-control', 'id' => 'title', 'maxlength' => 70]) }}
					</div>

					<div class="form-group">
					   <label for="description">Description <small>(<span class="l">0</span> / 140)</small></label>
					   {{ Form::text('description', empty($interview) ? null : $interview->description, ['class' => 'form-control', 'id' => 'description', 'maxlength' => 140]) }}
					</div>

					<div class="form-group">
					   <label for="keywords">Keywords <small>(<span class="l">0</span> / 140)</small></label>
					   {{ Form::text('keywords', empty($interview) ? null : $interview->keywords, ['class' => 'form-control', 'id' => 'keywords', 'maxlength' => 140]) }}
					</div>

					<div class="form-group">
						<label>Теги</label>
						{{ Form::select('tags[]', $tags, empty($interview) ? null : $interview->tags()->pluck('name', 'name'), ['multiple' => true, 'class' => 'form-control multiple_select', 'id' => 'name']) }}
					</div>

					<div class="form-group">
					   <label for="published_at">Дата публикации</label>
					   {{ Form::text('published_at', empty($interview) || !$interview->published_at ? null : $interview->published_at->format('Y-m-d H:i:s'), ['class' => 'form-control datetime_mask', 'id' => 'published_at']) }}
					</div>

					<div class="form-group">
						<label>Автор</label>
						{{ Form::select('authors[]', $authors, empty($interview) ? null : $interview->authors, ['class' => 'form-control', 'id' => 'authors']) }}
						{{-- {{ Form::select('authors[]', $authors, empty($interview) ? null : $interview->authors, ['multiple' => true, 'class' => 'form-control multiple_select', 'id' => 'authors']) }} --}}
					</div>

					@if (!empty($interview))
						<h3>Старые данные автора</h3>
						<p>
							<strong>ФИО:</strong> {{ $interview->author }} <br>
							<strong>Фото:</strong> {{ $interview->author_img }} <br>
							<img src="{{ $interview->author_img }}" alt="{{ $interview->author }}" width="100">
						</p>
					@endif


					<?php
						$disabled = 'disabled';
						$textru_text_unique = \App\Settings::byKey('textru_text_unique');
						$u2 = floatval(str_replace(',', '.', $textru_text_unique->value));
					?>
					@if (!empty($interview) && !$interview->published)
						@if($interview->textru_uid && $interview->textru)
							<?php
								$u1 = floatval($interview->textru_data['text_unique']);

								if ($u1 >= $u2) {
									$disabled = '';
								}
							?>
						@endif
					@elseif(!empty($interview) && $interview->published)
						<?php $disabled = ''; ?>
					@endif
					<div class="form-group">
						<label>
							<input type="hidden" name="published" value="0">
							<input type="checkbox" name="published" class="minimal" value="1" {{ !empty($interview) && $interview->published ? 'checked' : '' }} {{ $disabled }}>
							опубликовано
						</label>
						{!! $disabled ? '<span class="disabled_text">(опубликовать можно после того как уникальность текста будет более ' . $u2 . ')</span>' : '' !!}
					</div>

					<div class="form-group">
						<label>
							<input type="hidden" name="for_registered" value="0">
							<input type="checkbox" name="for_registered" class="minimal" value="1" {{ !empty($interview) && $interview->for_registered ? 'checked' : '' }}>
							только для зарегистрированных
						</label>
					</div>




					<div class="form-group">
						<label>
							<input type="hidden" name="main_slider" value="0">
							<input type="checkbox" name="main_slider" class="minimal" value="1" {{ !empty($interview) && $interview->main_slider ? 'checked' : '' }}>
							промо <small>(Выводить в слайдере на главной)</small>
						</label>
					</div>

					<div class="show_on_main">

						<div class="form-group">
						   <label for="main_slider_position">Позиция в главном слайдере</label>
						   {{ Form::text('main_slider_position', empty($interview) || !$interview->main_slider_position ? null : $interview->main_slider_position, ['class' => 'form-control', 'id' => 'main_slider_position']) }}
						</div>





						@include('admin.partials.cropper_multiple', [
							'object' => !empty($interview) ? $interview : null,
							'name' => 'interview2',
							'source_image' => 'main_slider_source_img',
							'cropper_images' => [
								['name' => 'main_slider_big_img', 'width' => 828, 'height' => 480],
								['name' => 'main_slider_sm_img', 'width' => 362, 'height' => 224]
							]
						])

					</div>






					@if (!empty($interview))
						<p>Просмотры: <b>{{ $interview->views }}</b></p>
					@endif

					<input type="hidden" name="check" value="0">


					<div class="text_ru">
						@if ($interview->textru_uid && !$interview->textru)
							<p>Текст анализируется</p>
							<div class="progress progress active">
								<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
								</div>
					        </div>

							<script>
								var check_in_progress = 1;
							</script>

							<a href="{{ route('textru_recheck', $interview->textru_uid) }}" class="btn btn-warning btn-sm">Запросить данные вручную</a>
							<br><br>
						@elseif($interview->textru_uid && $interview->textru)
							@include('admin.partials.textru', ['textru_data' => $interview->textru_data])
						@endif
					</div>



					<p>
						<a href="{{ route('interview.index') }}" class="btn btn-danger">Отмена</a>
						<a href="#" class="check btn btn-primary">Сохранить и проверить текст</a>
						<input type="submit" class="btn btn-success" value="Сохранить">

						@if (!empty($interview))
							<a href="{{ route('interviews.show' , $interview->alias) }}" target="_blank" class="btn btn-primary">
								<span class="fa fa-eye"></span> Перейти на страницу
							</a>
						@endif
					</p>
			   {!! Form::close() !!}
			</div>
		</div>
	</div>
@endsection

@section('scripts')
	<script src="/assets/admin_assets/plugins/cropperjs/cropper.min.js" charset="utf-8"></script>
	<script src="/vendor/laravel-filemanager/js/lfm.js"></script>
	<script type="text/javascript">
		$('#lfm').filemanager('image&wdir=/uploads/authors');
		$(function () {
			CKEDITOR.replace('text', {
				filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
				filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token=',
				filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
				filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token='
			});

			$('.check').click(function() {
				$('[name="check"]').val('1');
				$(this).closest('form').submit();
				return false;
			});

			check_text();
		});

		function check_text() {
			//if (typeof check_in_progress !== 'undefined') {
				$.get("{{ route('interview.check', $interview->id) }}", function(data) {
					if (data.check) {
						$('.text_ru').html(data.data);

						if (!data.disabled) {
							$('[name="published"]').prop('disabled', false).parent().removeClass('disabled');
							$('.disabled_text').hide();
						}

						$('.more_lnk').click(function() {
							$($(this).attr('href')).slideToggle();

							return false;
						});
					} else {
						setTimeout(check_text, 5000)
					}
				});
			//}
		}
	</script>
@endsection
