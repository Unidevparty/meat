@extends('layouts.admin')

@section('content')
	<link rel="stylesheet" href="/assets/admin_assets/plugins/cropperjs/cropper.min.css">
	<div class="box-body pad">
		<div class="box">
			<div class="box-body">
				@if (!empty($article))
					{!! Form::model($article, ['route' => ['article.update', $article->id], 'method' => 'PATCH', 'enctype' => 'multipart/form-data']) !!}
			    @else
					{!! Form::open(['route' => 'article.store', 'enctype' => 'multipart/form-data']) !!}
			    @endif
					<div class="form-group{{ $errors->has('name') ? ' has-error' :'' }}">
					   <label for="name">Название</label>
					   {{ Form::text('name', empty($article) ? null : $article->name, ['class' => 'form-control', 'id' => 'name']) }}
					</div>

					<div class="form-group">
					   <label for="text">Аннотация</label>
					   {{ Form::textarea('introtext', empty($article) ? null : $article->introtext, ['class' => 'form-control', 'rows' => 4]) }}
					</div>

					<div class="form-group">
					   <label for="text">Текст статьи</label>
					   {{ Form::textarea('text', empty($article) ? null : $article->text, ['class' => 'form-control', 'id' => 'text', 'rows' => 2]) }}
					</div>



					@include('admin.partials.cropper_multiple', [
						'object' => !empty($article) ? $article : null,
						'name' => 'article',
						'source_image' => 'source_image',
						'cropper_images' => [
							['name' => 'main_image', 'width' => 1216, 'height' => 495],
							['name' => 'preview_big', 'width' => 431, 'height' => 330],
							['name' => 'preview', 'width' => 296, 'height' => 227],
							//['name' => 'on_main', 'width' => 138, 'height' => 90],
							['name' => 'big_on_main_slider', 'width' => 828, 'height' => 480],
							['name' => 'sm_on_main_slider', 'width' => 362, 'height' => 224]
						]
					])

					@if ($article && $article->source_image)
						<a href="{{ route('article.delete_images', $article->id) }}" class="btn btn-danger confirm">Удалить картинки</a>
					@endif

					{{-- @include('admin.partials.cropper', ['name' => 'image', 'width' => 940, 'height' => 496])
					@include('admin.partials.cropper', ['name' => 'preview', 'width' => 296, 'height' => 227])
					@include('admin.partials.cropper', ['name' => 'on_main', 'width' => 138, 'height' => 90]) --}}


					<div class="form-group">
					   <label for="title">Title <small>(<span class="l">0</span> / 70)</small></label>
					   {{ Form::text('title', empty($article) ? null : $article->title, ['class' => 'form-control', 'id' => 'title', 'maxlength' => 70]) }}
					</div>

					<div class="form-group">
					   <label for="description">Description <small>(<span class="l">0</span> / 140)</small></label>
					   {{ Form::text('description', empty($article) ? null : $article->description, ['class' => 'form-control', 'id' => 'description', 'maxlength' => 140]) }}
					</div>

					<div class="form-group">
					   <label for="keywords">Keywords <small>(<span class="l">0</span> / 140)</small></label>
					   {{ Form::text('keywords', empty($article) ? null : $article->keywords, ['class' => 'form-control', 'id' => 'keywords', 'maxlength' => 140]) }}
					</div>

					<div class="form-group">
						<label>Разделы</label>
						{{ Form::select('tags[]', $tags, empty($article) ? null : $article->tags()->pluck('name', 'name'), ['multiple' => true, 'class' => 'form-control multiple_select', 'id' => 'tags']) }}
					</div>

					<div class="form-group">
					   <label for="published_at">Дата публикации</label>
					   {{ Form::text('published_at', empty($article) || !$article->published_at ? null : $article->published_at->format('Y-m-d H:i:s'), ['class' => 'form-control datetime_mask', 'id' => 'published_at']) }}
					</div>

					<div class="form-group">
						<label>Автор</label>
						{{ Form::select('authors[]', $authors, empty($article) ? null : $article->authors, ['multiple' => true, 'class' => 'form-control multiple_select', 'id' => 'authors']) }}
					</div>

					@if (!empty($article))
						<h3>Старые данные автора</h3>
						<p>
							<strong>ФИО:</strong> {{ $article->author }} <br>
							<strong>Фото:</strong> {{ $article->author_img }} <br>
							<img src="{{ $article->author_img }}" alt="{{ $article->author }}" width="100">
						</p>
					@endif

					<div class="form-group">
						<label>Компания</label>
						{{ Form::select('company', $companies, empty($article) ? null : $article->company()->id, ['class' => 'form-control select-custom', 'id' => 'company']) }}
					</div>

					<?php
						$disabled = 'disabled';
						$textru_text_unique = \App\Settings::byKey('textru_text_unique');
						$u2 = floatval(str_replace(',', '.', $textru_text_unique->value));
					?>
					@if (!empty($article) && !$article->published)
						@if($article->textru_uid && $article->textru)
							<?php
								$u1 = floatval($article->textru_data['text_unique']);

								if ($u1 >= $u2) {
									$disabled = '';
								}
							?>
						@endif
					@elseif(!empty($article) && $article->published)
						<?php $disabled = ''; ?>
					@endif
					<div class="form-group">
						<label>
							<input type="hidden" name="published" value="0">
							<input type="checkbox" name="published" class="minimal" value="1" {{ !empty($article) && $article->published ? 'checked' : '' }} {{ $disabled }}>
							опубликовано
						</label>
						{!! $disabled ? '<span class="disabled_text">(опубликовать можно после того как уникальность текста будет более ' . $u2 . ')</span>' : '' !!}
					</div>




					<div class="form-group">
						<label>
							<input type="hidden" name="for_registered" value="0">
							<input type="checkbox" name="for_registered" class="minimal" value="1" {{ !empty($article) && $article->for_registered ? 'checked' : '' }}>
							только для зарегистрированных
						</label>
					</div>

					@if (!empty($article))
						<p>Просмотры: <b>{{ $article->views }}</b></p>
					@endif

					<input type="hidden" name="check" value="0">


					<div class="text_ru">
						@if ($article->textru_uid && !$article->textru)
							<p>Текст анализируется</p>
							<div class="progress progress active">
								<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
								</div>
					        </div>

							<script>
								var check_in_progress = 1;
							</script>

							<a href="{{ route('textru_recheck', $article->textru_uid) }}" class="btn btn-warning btn-sm">Запросить данные вручную</a>
							<br><br>
						@elseif($article->textru_uid && $article->textru)
							@include('admin.partials.textru', ['textru_data' => $article->textru_data])
						@endif
					</div>


					<p>
						<a href="{{ route('article.index') }}" class="btn btn-danger">Отмена</a>
						<a href="#" class="check btn btn-primary">Сохранить и проверить текст</a>
						<input type="submit" class="btn btn-success" value="Сохранить">

						@if (!empty($article))
							<a href="{{ route('articles.show' , $article->alias) }}" target="_blank" class="btn btn-primary">
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
				$.get("{{ route('article.check', $article->id) }}", function(data) {
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
