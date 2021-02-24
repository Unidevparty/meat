@extends('layouts.admin')

@section('content')
	<link rel="stylesheet" href="/assets/admin_assets/plugins/cropperjs/cropper.min.css">
	<div class="box-body pad">
		<div class="box">
			<div class="box-body">
				@if (!empty($news))
					{!! Form::model($news, ['route' => ['news.update', $news->id], 'method' => 'PATCH', 'enctype' => 'multipart/form-data']) !!}
			    @else
					{!! Form::open(['route' => 'news.store', 'enctype' => 'multipart/form-data']) !!}
			    @endif
					<div class="form-group{{ $errors->has('name') ? ' has-error' :'' }}">
					   <label for="name">Название</label>
					   {{ Form::text('name', empty($news) ? null : $news->name, ['class' => 'form-control', 'id' => 'name']) }}
					</div>

					<div class="form-group">
					   <label for="text">Аннотация</label>
					   {{ Form::textarea('introtext', empty($news) ? null : $news->introtext, ['class' => 'form-control', 'rows' => 4]) }}
					</div>

					<div class="form-group">
					   <label for="text">Текст новости</label>
					   {{ Form::textarea('text', empty($news) ? null : $news->text, ['class' => 'form-control', 'id' => 'text', 'rows' => 2]) }}
					</div>


					@include('admin.partials.cropper_multiple', [
						'object' => !empty($news) ? $news : null,
						'name' => 'news',
						'source_image' => 'source_image',
						'cropper_images' => [
							['name' => 'main_image', 'width' => 940, 'height' => 496],
							['name' => 'preview', 'width' => 296, 'height' => 227],
							['name' => 'on_main', 'width' => 138, 'height' => 90]
						]
					])

					{{-- @include('admin.partials.cropper', ['name' => 'image', 'width' => 940, 'height' => 496])
					@include('admin.partials.cropper', ['name' => 'preview', 'width' => 296, 'height' => 227])
					@include('admin.partials.cropper', ['name' => 'on_main', 'width' => 138, 'height' => 90]) --}}


					<div class="form-group">
					   <label for="title">Title  <small>(<span class="l">0</span> / 70)</small></label>
					   {{ Form::text('title', empty($news) ? null : $news->title, ['class' => 'form-control', 'id' => 'title', 'maxlength' => 70]) }}
					</div>

					<div class="form-group">
					   <label for="description">Description  <small>(<span class="l">0</span> / 140)</small></label>
					   {{ Form::text('description', empty($news) ? null : $news->description, ['class' => 'form-control', 'id' => 'description', 'maxlength' => 140, 'size' => 1400]) }}
					</div>

					<div class="form-group">
					   <label for="keywords">Keywords  <small>(<span class="l">0</span> / 140)</small></label>
					   {{ Form::text('keywords', empty($news) ? null : $news->keywords, ['class' => 'form-control', 'id' => 'keywords', 'maxlength' => 140]) }}
					</div>

					<div class="form-group">
						<label>Теги</label>
						{{ Form::select('tags[]', $tags, empty($news) ? null : $news->tags()->pluck('name', 'name'), ['multiple' => true, 'class' => 'form-control multiple_select', 'id' => 'name']) }}
					</div>

					<div class="form-group">
					   <label for="source">Источник</label>
					   {{ Form::text('source', empty($news) || !$news->source ? null : $news->source, ['class' => 'form-control', 'id' => 'source']) }}
					</div>

					<div class="form-group">
					   <label for="published_at">Дата публикации</label>
					   {{ Form::text('published_at', empty($news) || !$news->published_at ? null : $news->published_at->format('Y-m-d H:i:s'), ['class' => 'form-control datetime_mask', 'id' => 'published_at']) }}
					</div>



					<div class="form-group">
						<label>Компания</label>
						{{ Form::select('company_id', $companies, empty($news) ? null : $news->company_id, ['class' => 'form-control select-custom', 'id' => 'company_id']) }}
					</div>

					<?php
						$disabled = 'disabled';
						$textru_text_unique = \App\Settings::byKey('textru_text_unique');
						$u2 = floatval(str_replace(',', '.', $textru_text_unique->value));
					?>
					@if (!empty($news) && !$news->published)
						@if($news->textru_uid && $news->textru)
							<?php
								$u1 = floatval($news->textru_data['text_unique']);

								if ($u1 >= $u2) {
									$disabled = '';
								}
							?>
						@endif
					@elseif(!empty($news) && $news->published)
						<?php $disabled = ''; ?>
					@endif
					<div class="form-group">
						<label>
							<input type="hidden" name="published" value="0">
							<input type="checkbox" name="published" class="minimal" value="1" {{ !empty($news) && $news->published ? 'checked' : '' }} {{ $disabled }}>
							опубликовано
						</label>
						{!! $disabled ? '<span class="disabled_text">(опубликовать можно после того как уникальность текста будет более ' . $u2 . ')</span>' : '' !!}
					</div>

					@if (!empty($news))
						<p>Просмотры: <b>{{ $news->views }}</b></p>
					@endif

					<input type="hidden" name="check" value="0">


					<div class="text_ru">
						@if ($news->textru_uid && !$news->textru)
							<p>Текст анализируется</p>
							<div class="progress progress active">
								<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
								</div>
					        </div>

							<script>
								var check_in_progress = 1;
							</script>

							<a href="{{ route('textru_recheck', $news->textru_uid) }}" class="btn btn-warning btn-sm">Запросить данные вручную</a>
							<br><br>
						@elseif($news->textru_uid && $news->textru)
							@include('admin.partials.textru', ['textru_data' => $news->textru_data])
						@endif
					</div>



					<p>
						<a href="{{ route('news.index') }}" class="btn btn-danger">Отмена</a>
						<a href="#" class="check btn btn-primary">Сохранить и проверить текст</a>
						<input type="submit" class="btn btn-success" value="Сохранить">

						@if (!empty($news))
							<a href="{{ route('news.show' , $news->alias) }}" target="_blank" class="btn btn-primary">
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
	<script type="text/javascript">
		$(function () {
			CKEDITOR.replace('text', {
				filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
				filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token=',
				filebrowserBrowseUrl: '/laravel-filemanager?type=Images',
				filebrowserUploadUrl: '/laravel-filemanager/upload?type=Images&_token='
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
				$.get("{{ route('news.check', $news->id) }}", function(data) {
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
