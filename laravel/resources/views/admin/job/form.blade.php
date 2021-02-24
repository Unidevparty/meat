@extends('layouts.admin')

@section('content')
	<link rel="stylesheet" href="/assets/admin_assets/plugins/cropperjs/cropper.min.css">
	<div class="box-body pad">
		<div class="box">
			<div class="box-body">
				@if (!empty($job))
					{!! Form::model($job, ['route' => ['job.update', $job->id], 'method' => 'PATCH', 'enctype' => 'multipart/form-data', 'class' => 'save-form']) !!}
			    @else
					{!! Form::open(['route' => 'job.store', 'enctype' => 'multipart/form-data', 'class' => 'save-form']) !!}
			    @endif
					<div class="form-group{{ $errors->has('name') ? ' has-error' :'' }}">
					   <label for="name">Название</label>
					   {{ Form::text('name', empty($job) ? null : $job->name, ['class' => 'form-control', 'id' => 'name']) }}
					</div>

					<div class="form-group">
					   <label for="title">Title <small>(<span class="l">0</span> / 70)</small></label>
					   {{ Form::text('title', empty($job) ? null : $job->title, ['class' => 'form-control', 'id' => 'title', 'maxlength' => 70]) }}
					</div>

					<div class="form-group">
					   <label for="description">Description <small>(<span class="l">0</span> / 140)</small></label>
					   {{ Form::text('description', empty($job) ? null : $job->description, ['class' => 'form-control', 'id' => 'description', 'maxlength' => 140]) }}
					</div>

					<div class="form-group">
					   <label for="keywords">Keywords <small>(<span class="l">0</span> / 140)</small></label>
					   {{ Form::text('keywords', empty($job) ? null : $job->keywords, ['class' => 'form-control', 'id' => 'keywords', 'maxlength' => 140]) }}
					</div>


					<div class="form-group">
						<label>
							<input type="hidden" name="published" value="0">
							<input type="checkbox" name="published" class="minimal" value="1" {{ !empty($job) && $job->published ? 'checked' : '' }}>
							опубликовано
						</label>
					</div>

					<div class="form-group">
					   <label for="published_at">Дата публикации</label>
					   {{ Form::text('published_at', empty($job) || !$job->published_at ? \Carbon\Carbon::now()->format('Y-m-d H:i:s') : $job->published_at->format('Y-m-d H:i:s'), ['class' => 'form-control datetime_mask', 'id' => 'published_at']) }}
					</div>

					<div class="form-group">
						<label>
							<input type="hidden" name="fixed" value="0">
							<input type="checkbox" name="fixed" class="minimal" value="1" {{ !empty($job) && $job->fixed ? 'checked' : '' }}>
							закрепить вакансию
						</label>
					</div>

					<div class="form-group">
					   <label for="fixed_to">Закреплено до</label>
					   {{ Form::text('fixed_to', empty($job) || !$job->fixed_to ? '' : $job->fixed_to->format('Y-m-d H:i:s'), ['class' => 'form-control datetime_mask', 'id' => 'fixed_to']) }}
					</div>

					<div class="form-group">
						<label class="close_label">
							<input type="hidden" name="closed" value="0">
							<input type="checkbox" name="closed" class="minimal close_checkbox" value="1" {{ !empty($job) && $job->closed ? 'checked' : '' }}>
							вакансия закрыта
						</label>
					</div>

					<div class="form-group close_why" style="display: {{ !empty($job) && $job->closed ? 'block' : 'none' }}">
						<label>Причина закрытия</label>
						{{ Form::select('close_id', $jobCloses, empty($job) ? null : $job->close_id, ['class' => 'form-control ', 'id' => 'close_id']) }}
					</div>

					<div class="form-group">
						<label>Компания</label>
						{{ Form::select('company_id', $companies, empty($job) ? null : $job->company_id, ['class' => 'form-control ', 'id' => 'company_id']) }}
					</div>

					<div class="form-group">
						<label>Тип компании</label>
						{{ Form::select('company_type_id', $company_types, empty($job) ? null : $job->company_type_id, ['class' => 'form-control ', 'id' => 'company_type_id']) }}
					</div>

					<div class="form-group">
					   <label for="introtext">Описание</label>
					   {{ Form::textarea('introtext', empty($job) ? null : $job->introtext, ['class' => 'form-control', 'id' => 'introtext']) }}
					</div>

					<div class="form-group">
					   <label for="city">Город</label>
					   {{ Form::text('city', empty($job) ? null : $job->city, ['class' => 'form-control', 'id' => 'city']) }}
					</div>

					<div class="form-group">
					   <label for="address">Адрес</label>
					   {{ Form::text('address', empty($job) ? null : $job->address, ['class' => 'form-control', 'id' => 'address']) }}
					</div>

					<div class="form-group">
						<label for="zarplata">Зарплата</label>
						{{ Form::text('zarplata', empty($job) ? null : $job->zarplata, ['class' => 'form-control', 'id' => 'zarplata']) }}

						@foreach ($zp_options as $zpop)
							<label>
								<input type="checkbox" name="zp_options[]" class="minimal" value="{{ $zpop }}" {{ !empty($job) && $job->hasZpOption($zpop) ? 'checked' : '' }}>
								{{ $zpop }}
							</label> <br>
						@endforeach
					</div>

					<div class="form-group">
						<label class="our_checkbox_label">
							<input type="hidden" name="our" value="0">
							<input type="checkbox" name="our" class="minimal our_checkbox" value="1" {{ !empty($job) && $job->our ? 'checked' : '' }}>
							наша вакансия
						</label>
					</div>

					<div class="form-group signature_wrap" style="display: {{ !empty($job) && $job->our ? 'none' : 'block' }}">
					   <label for="signature">Подпись</label>
					   {{ Form::textarea('signature', empty($job) ? null : $job->signature, ['class' => 'form-control', 'id' => 'signature']) }}
					</div>

					<div class="form-group">
					   <label for="visibility">Доступно для групп</label>
					   <br>
					   @foreach ($groups as $group)
						   <label class="group_label">
							   <input type="checkbox" name="groups[]" class="minimal" value="{{ $group['id'] }}" {{ !empty($job) ? $job->hasGroup($group['id']) ? 'checked' : '' : 'checked'}}>
							   {!! $group['formattedName'] !!}
						   </label>
					   @endforeach
					   <div class="clear"></div>
					</div>

					<div class="form-group">
					   <label for="obyazannosti">Обязанности</label>
					   {{ Form::textarea('obyazannosti', empty($job) ? null : $job->obyazannosti, ['class' => 'form-control', 'id' => 'obyazannosti']) }}
					</div>

					<div class="form-group">
					   <label for="trebovaniya">Требования</label>
					   {{ Form::textarea('trebovaniya', empty($job) ? null : $job->trebovaniya, ['class' => 'form-control', 'id' => 'trebovaniya']) }}
					</div>

					<div class="form-group">
					   <label for="usloviya">Условия</label>
					   {{ Form::textarea('usloviya', empty($job) ? null : $job->usloviya, ['class' => 'form-control', 'id' => 'usloviya']) }}
					</div>




					@if (!empty($job))
						<p>Просмотры: <b>{{ $job->views }}</b></p>
					@endif

					<p>
						<a href="{{ route('job.index') }}" class="btn btn-danger">Отмена</a>
						<input type="submit" class="btn btn-success" value="Сохранить">

						@if (!empty($job))
							<a href="{{ route('job.show' , $job->alias) }}" target="_blank" class="btn btn-primary">
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
		var editors = [
			'introtext',
			'signature',
			'obyazannosti',
			'trebovaniya',
			'usloviya',
		];
		$(function () {
			for (var i = 0; i < editors.length; i++) {
				CKEDITOR.replace(editors[i], {
					filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
					filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token=',
					filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
					filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token='
				});
			}



			$('.close_label').click(function() {
				if ($('.close_checkbox').is(':checked')) {
					$('.close_why').slideDown();
				} else {
					$('.close_why').slideUp();
				}
			});

			$('.our_checkbox_label').click(function() {
				if ($('.our_checkbox').is(':checked')) {
					$('.signature_wrap').slideUp();
				} else {
					$('.signature_wrap').slideDown();
				}
			});
		});
	</script>
@endsection
