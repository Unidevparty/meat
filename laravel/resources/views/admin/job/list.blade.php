@extends('layouts.admin')

@section('content')
	<p>
		<a href="{{ route('job.create') }}" class="btn btn-success">Создать вакансию</a>
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
	@if ($jobs->count())
		<table class="table table-striped">
			<tbody>
				<tr>
					<th style="width: 10px">#</th>
					<th>Название</th>
					<th>Опубликовано</th>
					<th>Закреплен</th>
					<th>Дата публикации</th>
					<th>Дата изменения</th>
					<th>Дата создания</th>
					<th width="140"></th>
					<th width="40">Просмотры</th>
				</tr>
				@foreach ($jobs as $job)
					<tr {!! $job->fixed && !$job->isfixed ? 'class="warning"' : '' !!}>
						<td>{{ $job->id }}</td>
						<td>{{ $job->name }}</td>
						<td class="text-center"><span class="fa {{ $job->published ? 'fa-check' : 'fa-close' }}"></span></td>
						<td class="text-center"><span class="fa {{ $job->fixed ? 'fa-check' : 'fa-close' }}"></span>{{ $job->fixed && !$job->isfixed ? ' просрочена' : '' }}</td>
						<td>{{ $job->published_at ? $job->published_at->format('Y-m-d H:i:s') : '' }}</td>
						<td>{{ $job->updated_at->format('Y-m-d H:i:s') }}</td>
						<td>{{ $job->created_at->format('Y-m-d H:i:s') }}</td>
						<td>
							<a href="{{ route('job.show' , $job->alias) }}" target="_blank" class="btn btn-primary">
                                <span class="fa fa-eye"></span>
                            </a>

							<a href="{{ route('job.edit' , $job->id) }}" class="btn btn-primary">
                                <span class="fa fa-pencil"></span>
                            </a>

                            {!! Form::open(['route' => ['job.destroy', $job->id], 'method' => 'DELETE', 'class' => 'inline-block']) !!}
                                <button type="submit" class="btn delete btn-danger">
                                    <span class="fa fa-close"></span>
                                </button>
                            {!! Form::close() !!}
						</td>
						<td class="text-center"><span class="badge bg-light-blue">{{ $job->views }}</span></td>
					</tr>
				@endforeach
			</tbody>
		</table>

		{!! $jobs->render() !!}
	@endif
@endsection

@section('scripts')
    <script src="/assets/admin_assets/plugins/autocomplete/jquery.autocomplete.min.js"></script>
	<script>
		// Подсказки для поиска
		$('.search_by_name').autocomplete({
		    serviceUrl: '{{ route('job.search') }}',
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
