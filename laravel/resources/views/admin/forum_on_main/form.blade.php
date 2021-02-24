@extends('layouts.admin')

@section('content')
	<link rel="stylesheet" href="/assets/admin_assets/plugins/cropperjs/cropper.min.css">
	<div class="box-body pad">
		<div class="box">
			<div class="box-body">
				@if (!empty($forum))
					{!! Form::model($forum, ['route' => ['forum_on_main.update', $forum->id], 'method' => 'PATCH', 'enctype' => 'multipart/form-data']) !!}
			    @else
					{!! Form::open(['route' => 'forum_on_main.store', 'enctype' => 'multipart/form-data']) !!}
			    @endif
					<div class="form-group{{ $errors->has('name') ? ' has-error' :'' }}">
					   <label for="name">Название <small>(для админки)</small></label>
					   {{ Form::text('name', empty($forum) ? null : $forum->name, ['class' => 'form-control', 'id' => 'name']) }}
					</div>

					<div class="form-group{{ $errors->has('forum_id') ? ' has-error' :'' }}">
					   <label for="forum_id">ID темы</label>
					   {{ Form::text('forum_id', empty($forum) ? null : $forum->forum_id, ['class' => 'form-control', 'id' => 'forum_id']) }}
					</div>

					<div class="form-group{{ $errors->has('position') ? ' has-error' :'' }}">
					   <label for="position">Позиция</label>
					   {{ Form::number('position', empty($forum) ? 1 : $forum->position, ['class' => 'form-control', 'id' => 'position', 'min' => '1']) }}
					</div>




					@include('admin.partials.cropper_multiple', [
						'object' => !empty($forum) ? $forum : null,
						'name' => 'forum',
						'source_image' => 'source_image',
						'cropper_images' => [
							['name' => 'image', 'width' => 296, 'height' => 227],
							['name' => 'big_on_main_slider', 'width' => 828, 'height' => 480],
							['name' => 'sm_on_main_slider', 'width' => 362, 'height' => 224]
						]
					])


					


					<p>
						<a href="{{ route('forum_on_main.index') }}" class="btn btn-danger">Отмена</a>
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
