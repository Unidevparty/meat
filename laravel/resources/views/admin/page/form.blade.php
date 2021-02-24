@extends('layouts.admin')

@section('content')
	<div class="box-body pad">
		<div class="box">
			<div class="box-body">
				@if (!empty($page))
					{!! Form::model($page, ['route' => ['page.update', $page->id], 'method' => 'PATCH', 'enctype' => 'multipart/form-data']) !!}
			    @else
					{!! Form::open(['route' => 'page.store', 'enctype' => 'multipart/form-data']) !!}
			    @endif
					<div class="form-group{{ $errors->has('name') ? ' has-error' :'' }}">
					   <label for="name">Название</label>
					   {{ Form::text('name', empty($page) ? null : $page->name, ['class' => 'form-control', 'id' => 'name']) }}
					</div>

					<div class="form-group{{ $errors->has('url') ? ' has-error' :'' }}">
					   <label for="url">Адрес</label>
					   {{ Form::text('url', empty($page) ? null : $page->url, ['class' => 'form-control', 'id' => 'url']) }}
					</div>

					<div class="form-group{{ $errors->has('template') ? ' has-error' :'' }}">
					   <label for="template">Шаблон</label>
					   {{ Form::select('template', $templates, empty($page) ? 'default' : $page->template, ['class' => 'form-control', 'id' => 'template']) }}
					</div>

					<div class="form-group">
					   <label for="title">Title</label>
					   {{ Form::text('title', empty($news) ? null : $news->title, ['class' => 'form-control', 'id' => 'title']) }}
					</div>

					<div class="form-group">
					   <label for="description">Description</label>
					   {{ Form::text('description', empty($news) ? null : $news->description, ['class' => 'form-control', 'id' => 'description']) }}
					</div>

					<div class="form-group">
					   <label for="keywords">Keywords</label>
					   {{ Form::text('keywords', empty($news) ? null : $news->keywords, ['class' => 'form-control', 'id' => 'keywords']) }}
					</div>

					<div class="form-group">
					   <label for="text">Текст</label>
					   {{ Form::textarea('text', empty($news) ? null : $news->text, ['class' => 'form-control', 'id' => 'text', 'rows' => 5]) }}
					</div>

					<div class="form-group">
					   <label for="published_at">Дата публикации</label>
					   {{ Form::text('published_at', empty($news) || !$news->published_at ? Carbon\Carbon::now()->format('Y-m-d H:i:s') : $news->published_at->format('Y-m-d H:i:s'), ['class' => 'form-control datetime_mask', 'id' => 'published_at']) }}
					</div>

					<div class="form-group">
						<label>
							<input type="hidden" name="published" value="0">
							<input type="checkbox" name="published" class="minimal" value="1" {{ !empty($page) && $page->published ? 'checked' : '' }}>
							опубликовано
						</label>
					</div>

					@if (!empty($page))
						<p>Просмотры: <b>{{ $page->views }}</b></p>
					@endif

					<p>
						<a href="{{ route('page.index') }}" class="btn btn-danger">Отмена</a>
						<input type="submit" class="btn btn-success" value="Сохранить">

                        @if (!empty($page))
							<a href="{{ $page->url }}" target="_blank" class="btn btn-primary">
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
				filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
				filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token='
			});
		});
	</script>
@endsection
