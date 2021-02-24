@extends('layouts.admin')

@section('content')
	<link rel="stylesheet" href="/assets/admin_assets/plugins/cropperjs/cropper.min.css">
	<div class="box-body pad">
		<div class="box">
			<div class="box-body">
				@if (!empty($banner))
					{!! Form::model($banner, ['route' => ['banner.update', $banner->id], 'method' => 'PATCH', 'enctype' => 'multipart/form-data']) !!}
			    @else
					{!! Form::open(['route' => 'banner.store', 'enctype' => 'multipart/form-data']) !!}
			    @endif
					<div class="form-group{{ $errors->has('name') ? ' has-error' :'' }}">
					   <label for="name">Название</label>
					   {{ Form::text('name', empty($banner) ? null : $banner->name, ['class' => 'form-control', 'id' => 'name']) }}
					</div>

					<div class="form-group">
					   <label for="start_date">Дата публикации</label>
					   {{ Form::text('start_date', empty($banner) || !$banner->start_date ? null : $banner->start_date->format('Y-m-d H:i:s'), ['class' => 'form-control datetime_mask', 'id' => 'start_date']) }}
					</div>

					<div class="form-group">
					   <label for="end_date">Дата окончания</label>
					   {{ Form::text('end_date', empty($banner) || !$banner->end_date ? null : $banner->end_date->format('Y-m-d H:i:s'), ['class' => 'form-control datetime_mask', 'id' => 'end_date']) }}
					</div>

					
					<div class="form-group{{ $errors->has('number') ? ' has-error' :'' }}">
					   <label for="number">Медиа-кит</label>
					   {{ Form::text('number', empty($banner) ? null : $banner->number, ['class' => 'form-control', 'id' => 'number']) }}
					</div>

					<div class="form-group{{ $errors->has('url') ? ' has-error' :'' }}">
					   <label for="url">Ссылка</label>
					   {{ Form::text('url', empty($banner) ? null : $banner->url, ['class' => 'form-control', 'id' => 'url']) }}
					</div>

					<div class="form-group">
						<label>Позиция</label>
						{{ Form::select('position', $positions, empty($interview) ? null : $interview->position, ['class' => 'form-control ', 'id' => 'position']) }}
					</div>

					
					
					<div class="form-group{{ $errors->has('main_image') ? ' has-error' :'' }}">
						<label for="main_image">Основной баннер</label>
						<input type="file" name="main_image" accept="image/x-png,image/gif,image/jpeg">
						@if (isset($banner) && $banner->main_image)
							<p><img src="{{ $banner->main_image }}" width="100"></p>
						@endif
					</div>


					<div class="form-group{{ $errors->has('mobile_image') ? ' has-error' :'' }}">
						<label for="mobile_image">Баннер для мобильной версии</label>
						<input type="file" name="mobile_image" accept="image/x-png,image/gif,image/jpeg">
						@if (isset($banner) && $banner->mobile_image)
							<p><img src="{{ $banner->mobile_image }}" width="100"></p>
						@endif
					</div>

					
					<div class="form-group{{ $errors->has('tablet_image') ? ' has-error' :'' }}">
						<label for="tablet_image">Баннер для планшетной версии (Только для A3)</label>
						<input type="file" name="tablet_image" accept="image/x-png,image/gif,image/jpeg">
						@if (isset($banner) && $banner->tablet_image)
							<p><img src="{{ $banner->tablet_image }}" width="100"></p>
						@endif
					</div>

					<div class="form-group">
						<label>
							<input type="hidden" name="published" value="0">
							<input type="checkbox" name="published" class="minimal" value="1" {{ !empty($banner) && $banner->published ? 'checked' : '' }}>
							опубликовано
						</label>
					</div>


					<div class="form-group">
						<label>
							<input type="hidden" name="bydefault" value="0">
							<input type="checkbox" name="bydefault" class="minimal" value="1" {{ !empty($banner) && $banner->bydefault ? 'checked' : '' }}>
							по умолчанию
						</label>
					</div>




					@if (!empty($banner))
						<p>Просмотры: <b>{{ $banner->views }}</b></p>
					@endif

					@if (!empty($banner))
						<p>Клики: <b>{{ $banner->clicks }}</b></p>
					@endif



					<p>
						<a href="{{ route('banner.index') }}" class="btn btn-danger">Отмена</a>
						<input type="submit" class="btn btn-success" value="Сохранить">
					</p>
			   {!! Form::close() !!}
			</div>
		</div>
	</div>
@endsection

@section('scripts')
	<script src="/assets/admin_assets/plugins/cropperjs/cropper.min.js" charset="utf-8"></script>
@endsection
