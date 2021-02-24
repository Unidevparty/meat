@extends('layouts.admin')

@section('content')
	<p>
		<a href="{{ route('interview.create') }}" class="btn btn-success">Создать интервью</a>

		<span class="col-md-8 pull-right">
			<span class="col-md-2">
				<input class="form-control date_mask" name="start" placeholder="YYYY-MM-DD" value="{{ $start }}">
			</span>
			<span class="col-md-2">
				<input class="form-control date_mask" name="end" placeholder="YYYY-MM-DD" value="{{ $end }}">
			</span>
			<span class="col-md-2">
				<a href="#" class="btn btn-primary search_by_date"><span class="fa fa-search"></span></a>
				<a href="#" class="btn btn-danger clear"><span class="fa fa-close"></span></a>
			</span>
			<span class="col-md-6">
				<input class="form-control search_by_name" placeholder="Поиск по названию">
			</span>
		</span>
	</p>
	<hr>
	@if ($interview && $interview->count())
		<table class="table table-striped">
			<tbody>
				<tr>
					<th style="width: 10px">#</th>
					<th>ФИО</th>
					<th class="text-center">Проверка <br> текста</th>
					<th>Опубликовано</th>
					<th>Дата публикации</th>
					<th>Дата изменения</th>
					<th>Дата создания</th>
					<th width="240"></th>
					<th width="40">Просмотры</th>
				</tr>
				@foreach ($interview as $new)
					<tr>
						<td>{{ $new->id }}</td>
						<td>{{ $new->fio }}</td>
						<?php
							$icon = 'fa-close';
				            
				            if ($new->textru_uid && $new->textru) {
				            	$icon = 'fa-check';
				            } elseif ($new->textru_uid) {
				                $icon = 'fa-spinner wheel';
				            }
						?>
						<td class="text-center"><span class="fa {{ $icon }}"></span></td>
						<td class="text-center"><span class="fa {{ $new->published ? 'fa-check' : 'fa-close' }}"></span></td>
						<td>{{ $new->published_at->format('Y-m-d H:i:s') }}</td>
						<td>{{ $new->updated_at->format('Y-m-d H:i:s') }}</td>
						<td>{{ $new->created_at->format('Y-m-d H:i:s') }}</td>
						<td>
							{!! Form::open(['route' => ['download.save', $new->id], 'method' => 'post', 'class' => 'inline-block']) !!}
							<input type="hidden" name="category" value="interview" />
							<input type="hidden" name="published" value="{{ $new->published_at->format('Y-m-d H:i:s') }}" />
							<input type="hidden" name="url" value="{{ route('interviews.show' , $new->alias) }}" />
							<button type="submit" class="btn btn-success">
								<span class="fa fa-save"></span>
							</button>
							{!! Form::close() !!}
							<a href="{{ route('interview.statistics' , $new->id) }}" class="btn btn-success">
								<span class="fa fa-eye"></span>
							</a>
							<a href="{{ route('interviews.show' , $new->alias) }}" target="_blank" class="btn btn-primary">
                                <span class="fa fa-eye"></span>
                            </a>

							<a href="{{ route('interview.edit' , $new->id) }}" class="btn btn-primary">
                                <span class="fa fa-pencil"></span>
                            </a>

                            {!! Form::open(['route' => ['interview.destroy', $new->id], 'method' => 'DELETE', 'class' => 'inline-block']) !!}
                                <button type="submit" class="btn delete btn-danger">
                                    <span class="fa fa-close"></span>
                                </button>
                            {!! Form::close() !!}
						</td>
						<td class="text-center"><span class="badge bg-light-blue">{{ $new->views }}</span></td>
					</tr>
				@endforeach
			</tbody>
		</table>

		{!! $interview->render() !!}
	@endif
@endsection

@section('scripts')
    <script src="/assets/admin_assets/plugins/autocomplete/jquery.autocomplete.min.js"></script>
	<script>
		// Подсказки для поиска
		$('.search_by_name').autocomplete({
		    serviceUrl: '{{ route('interview.search') }}',
		    onSelect: function(suggestion) {
		        // При клике открываем соотв страницу
		        location.href = suggestion.data;
		    }
		});


		$('.search_by_date').click(function() {
			var start = $('[name="start"]').val();
			var end = $('[name="end"]').val();

			location.href = location.href.split('?')[0] + '?start=' + start + '&end=' + end;

			return false;
		});

		$('.clear').click(function() {
			location.href = location.href.split('?')[0];

			return false;
		});
	</script>
@endsection