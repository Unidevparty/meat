@extends('layouts.admin')

@section('content')
	<div class="box-body pad">
		<div class="box">
			<div class="box-body">
				{!! Form::open(['route' => 'settings.meta.save', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) !!}

					@foreach ($metas as $meta)
						<h3>{{ $meta->name }}</h3>
						<div class="form-group">
							<label for="url" class="col-sm-3 control-label">Адрес страницы</label>
							<div class="col-sm-9">
								{{ Form::text('data[' . $meta->id . '][url]', $meta->url, ['class' => 'form-control', 'id' => 'url']) }}
							</div>
						</div>

						<div class="form-group">
							<label for="title" class="col-sm-3 control-label">Тайтл <small>(<span class="l">0</span> / 70)</small></label>
							<div class="col-sm-9">
								{{ Form::text('data[' . $meta->id . '][title]', $meta->title, ['class' => 'form-control', 'id' => 'title', 'maxlength'=>'70', 'size'=>'70']) }}
							</div>
						</div>

						<div class="form-group">
							<label for="keywords" class="col-sm-3 control-label">Ключевые слова <small>(<span class="l">0</span> / 140)</small></label>
							<div class="col-sm-9">
								{{ Form::text('data[' . $meta->id . '][keywords]', $meta->keywords, ['class' => 'form-control', 'id' => 'keywords', 'maxlength'=>'140', 'size'=>'140']) }}
							</div>
						</div>

						<div class="form-group">
							<label for="description" class="col-sm-3 control-label">Описание <small>(<span class="l">0</span> / 140)</small></label>
							<div class="col-sm-9">
								{{ Form::text('data[' . $meta->id . '][description]', $meta->description, ['class' => 'form-control', 'id' => 'description', 'maxlength'=>'140', 'size'=>'140']) }}
							</div>
						</div>

						<hr>
					@endforeach
					<div class="metas"></div>

					<p>
						<a href="#" class="btn new_meta btn-default">Добавить</a>
						<input type="submit" class="btn btn-success" value="Сохранить">
					</p>
			   {!! Form::close() !!}
			</div>
		</div>
	</div>
	<div class="hidden new_meta_box_wrapper">
		<div class="new_box">
			<div class="form-group">
				<label  class="col-sm-3 control-label">Название страницы</label>
				<div class="col-sm-9">
					<input type="text" name="new[][name]" class="form-control">
				</div>
			</div>
			<div class="form-group">
				<label  class="col-sm-3 control-label">Адрес страницы</label>
				<div class="col-sm-9">
					<input type="text" name="new[][url]" class="form-control">
				</div>
			</div>

			<div class="form-group">
				<label  class="col-sm-3 control-label">Тайтл <small>(<span class="l">0</span> / 70)</small></label>
				<div class="col-sm-9">
					<input type="text" name="new[][title]" class="form-control" maxlength="70" size="70">
				</div>
			</div>

			<div class="form-group">
				<label  class="col-sm-3 control-label">Ключевые слова <small>(<span class="l">0</span> / 140)</small></label>
				<div class="col-sm-9">
					<input type="text" name="new[][keywords]" class="form-control" maxlength="140" size="140">
				</div>
			</div>

			<div class="form-group">
				<label  class="col-sm-3 control-label">Описание <small>(<span class="l">0</span> / 140)</small></label>
				<div class="col-sm-9">
					<input type="text" name="new[][description]" class="form-control" maxlength="140" size="140">
				</div>
			</div>

			<hr>
		</div>
	</div>
@endsection

@section('scripts')
	<script type="text/javascript">
		$(function () {
			$('.new_meta').click(function() {
				var id = $('.metas .new_box').length;
				var new_form = $('.new_meta_box_wrapper .new_box').clone();
				$('input', new_form).each(function() {
					var name = $(this).attr('name');
					$(this).attr('name', name.replace('new[]', 'new[' + id + ']'))
				});
				$('[size="140"], [size="70"]', new_form).each(function() {
					chk_count($(this));
					$(this).on('keyup', function() {
						chk_count($(this));
					});
				});
				$('.metas').append(new_form);
			});

			$('[size="140"], [size="70"]').each(function() {
				chk_count($(this));
				$(this).on('keyup', function() {
					chk_count($(this));
				});
			});
		});

		
	</script>
@endsection