@extends('layouts.admin')

@section('content')
	<p>
		<a href="{{ route('article.create') }}" class="btn btn-success">Создать статью</a>
		
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
	@if ($articles->count())
		<table class="table table-striped">
			<tbody>
				<tr>
					<th style="width: 10px">#</th>
					<th>Название</th>
					<th class="text-center">Проверка <br> текста</th>
					<th>Опубликовано</th>
					<th>Дата публикации</th>
					<th>Дата изменения</th>
					<th>Дата создания</th>
					<th width="240"></th>
					<th width="40">Просмотры</th>
				</tr>
				@foreach ($articles as $article)
					<tr>
						<td>{{ $article->id }}</td>
						<td>{{ $article->name }}</td>
						<?php
							$icon = 'fa-close';
				            
				            if ($article->textru_uid && $article->textru) {
				            	$icon = 'fa-check';
				            } elseif ($article->textru_uid) {
				                $icon = 'fa-spinner wheel';
				            }
						?>
						<td class="text-center"><span class="fa {{ $icon }}"></span></td>
						<td class="text-center"><span class="fa {{ $article->published ? 'fa-check' : 'fa-close' }}"></span></td>
						<td>{{ $article->published_at->format('Y-m-d H:i:s') }}</td>
						<td>{{ $article->updated_at->format('Y-m-d H:i:s') }}</td>
						<td>{{ $article->created_at->format('Y-m-d H:i:s') }}</td>
						<td>
							<!-- <button type="button" class="btn btn-success" onclick="unload({{ $article->id }})">
                                <span class="fa fa-save"></span>
                            </button> -->
							{!! Form::open(['route' => ['download.save', $article->id], 'method' => 'post', 'class' => 'inline-block']) !!}
								<input type="hidden" name="category" value="articles" />
								<input type="hidden" name="published" value="{{ $article->published_at->format('Y-m-d H:i:s') }}" />
								<input type="hidden" name="url" value="{{ route('articles.show' , $article->alias) }}" />
                                <button type="submit" class="btn btn-success">
									<span class="fa fa-save"></span>
                                </button>
                            {!! Form::close() !!}

							<a href="{{ route('article.statistics' , $article->id) }}" class="btn btn-success">
								<span class="fa fa-eye"></span>
							</a>

							<a href="{{ route('articles.show' , $article->alias) }}" target="_blank" class="btn btn-primary">
                                <span class="fa fa-eye"></span>
                            </a>

							<a href="{{ route('article.edit' , $article->id) }}" class="btn btn-primary">
                                <span class="fa fa-pencil"></span>
                            </a>

                            {!! Form::open(['route' => ['article.destroy', $article->id], 'method' => 'DELETE', 'class' => 'inline-block']) !!}
                                <button type="submit" class="btn delete btn-danger">
                                    <span class="fa fa-close"></span>
                                </button>
                            {!! Form::close() !!}
						</td>
						<td class="text-center"><span class="badge bg-light-blue">{{ $article->views }}</span></td>
					</tr>
				@endforeach
			</tbody>
		</table>

		{!! $articles->render() !!}
	@endif
@endsection



@section('scripts')
    <script src="/assets/admin_assets/plugins/autocomplete/jquery.autocomplete.min.js"></script>
	<script>
		// Подсказки для поиска
		$('.search_by_name').autocomplete({
		    serviceUrl: '{{ route('article.search') }}',
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

		function unload(id)
		{
			$.ajax({
				url: '/unload_statistic',
				method: 'GET',
				data: {
					article_id: id,
					category: 'articles'
				},
				success: function(json){
					console.log(json);
				}
			});
		}

	</script>
@endsection
