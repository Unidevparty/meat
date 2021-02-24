@extends('layouts.admin')

@section('content')
	<link rel="stylesheet" href="/assets/admin_assets/plugins/cropperjs/cropper.min.css">
	<div class="box-body pad">
		<div class="box">
			<div class="box-body">
				@if (!empty($photogallery))
					{!! Form::model($photogallery, ['route' => ['photogallery.update', $photogallery->id], 'method' => 'PATCH', 'enctype' => 'multipart/form-data']) !!}
			    @else
					{!! Form::open(['route' => 'photogallery.store', 'enctype' => 'multipart/form-data']) !!}
			    @endif
					<div class="form-group{{ $errors->has('name') ? ' has-error' :'' }}">
					   <label for="name">Название</label>
					   {{ Form::text('name', empty($photogallery) ? null : $photogallery->name, ['class' => 'form-control', 'id' => 'name']) }}
					</div>

					<div class="form-group">
					   <label for="title">Title <small>(<span class="l">0</span> / 70)</small></label>
					   {{ Form::text('title', empty($photogallery) ? null : $photogallery->title, ['class' => 'form-control', 'id' => 'title', 'maxlength' => 70]) }}
					</div>

					<div class="form-group">
					   <label for="description">Description <small>(<span class="l">0</span> / 140)</small></label>
					   {{ Form::text('description', empty($photogallery) ? null : $photogallery->description, ['class' => 'form-control', 'id' => 'description', 'maxlength' => 140, 'size' => 1400]) }}
					</div>

					<div class="form-group">
					   <label for="keywords">Keywords <small>(<span class="l">0</span> / 140)</small></label>
					   {{ Form::text('keywords', empty($photogallery) ? null : $photogallery->keywords, ['class' => 'form-control', 'id' => 'keywords', 'maxlength' => 140]) }}
					</div>

					<div class="form-group">
					   <label for="text">Аннотация</label>
					   {{ Form::textarea('introtext', empty($photogallery) ? null : $photogallery->introtext, ['class' => 'form-control', 'rows' => 4]) }}
					</div>

					<div class="form-group">
					   <label for="text">Текст</label>
					   {{ Form::textarea('text', empty($photogallery) ? null : $photogallery->text, ['class' => 'form-control', 'id' => 'text', 'rows' => 2]) }}
					</div>

					{{-- <div class="form-group">
						<label for="image">Фото</label>
						<div class="input-group">
				            <span class="input-group-btn">
				            <a data-input="image" data-preview="holder" class="btn btn-primary image-lfm">
				                <i class="fa fa-picture-o"></i>
				            </a>
				            </span>
				            <input id="image" class="form-control" type="text" name="image" value="{{ $photogallery->image or '' }}">
				        </div>

						<img src="{{ $photogallery->image or '' }}" alt="" id="photogallery_image" class="object_image">
						<div id="photogallery_image_meta"></div>
			        </div> --}}

					@include('admin.partials.cropper_multiple', [
						'object' => !empty($photogallery) ? $photogallery : null,
						'name' => 'article',
						'source_image' => 'image',
						'cropper_images' => [
							['name' => 'main_image', 'width' => 940, 'height' => 495],
							['name' => 'category_image', 'width' => 457, 'height' => 309],
							['name' => 'home_image_1', 'width' => 411, 'height' => 291],
							['name' => 'home_image_2', 'width' => 411, 'height' => 209],
						]
					])

					<div class="gallery_wrap">
						<h3>Фотографии</h3>

            			<p><input type="file" id="photos" name="gallery_photos[]" multiple/></p>

						<div class="gallery_container">
							@if ($photogallery->photos)
									@foreach ($photogallery->photos as $id => $photo)
										@include('admin.photogallery.photo_row', [
											'id' => $id,
											'photo' => $photo
										])
									@endforeach
							@endif
						</div>

						<div class="proto hidden">
							@include('admin.photogallery.photo_row', [
								'id' => $id + 1,
								'photo' => null
							])
						</div>
					</div>

					<div class="form-group">
						<label>Теги</label>
						{{ Form::select('tags[]', $tags, empty($photogallery) ? null : $photogallery->tags()->pluck('name', 'name'), ['multiple' => true, 'class' => 'form-control multiple_select', 'id' => 'name']) }}
					</div>

					<div class="form-group">
						<label>Компания</label>
						{{ Form::select('company_id', $companies, empty($photogallery) ? null : $photogallery->company_id, ['class' => 'form-control select-custom', 'id' => 'company_id']) }}
					</div>

					<div class="form-group">
						<label>События</label>
						{{ Form::select('event_id', $events, empty($photogallery) ? null : $photogallery->event_id, ['class' => 'form-control select-custom', 'id' => 'event_id']) }}
					</div>

					<div class="form-group">
					   <label for="published_at">Дата публикации</label>
					   {{ Form::text('published_at', empty($photogallery) || !$photogallery->published_at ? Carbon\Carbon::now()->format('Y-m-d H:i:s') : $photogallery->published_at->format('Y-m-d H:i:s'), ['class' => 'form-control datetime_mask', 'id' => 'published_at']) }}
					</div>

					<div class="form-group">
						<label>
							<input type="hidden" name="published" value="0">
							<input type="checkbox" name="published" class="minimal" value="1" {{ !empty($photogallery) && $photogallery->published ? 'checked' : '' }} {{ $disabled }}>
							опубликовано
						</label>
					</div>

					@if (!empty($photogallery))
						<p>Просмотры: <b>{{ $photogallery->views }}</b></p>
					@endif

					<p>
						<a href="{{ route('photogallery.index') }}" class="btn btn-danger">Отмена</a>
						<input type="submit" class="btn btn-success submit_btn" value="Сохранить">
						@if (!empty($photogallery))
							<a href="{{ route('photogallery.show' , $photogallery->alias) }}" target="_blank" class="btn btn-primary">
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
	<script src="/assets/admin_assets/plugins/jquery-sortable.js" charset="utf-8"></script>
	<script src="/vendor/laravel-filemanager/js/lfm.js"></script>

	<script src="/assets/admin_assets/js/photogallery.js" charset="utf-8"></script>
@endsection
