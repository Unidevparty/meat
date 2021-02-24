@extends('layouts.admin')

@section('content')

    <div class="row">
        <form action="{{ route('searchlog.index') }}" method="get">

                <span class="col-md-2">
                    <input class="form-control date_mask" name="start" placeholder="YYYY-MM-DD" value="{{ request()->get('start') }}">
                </span>
                <span class="col-md-2">
                    <input class="form-control date_mask" name="end" placeholder="YYYY-MM-DD" value="{{ request()->get('end') }}">
                </span>
                <span class="col-md-2">
                    <button type="submit" class="btn btn-primary"><span class="fa fa-search"></span></button>
                    <a href="{{ route('searchlog.index') }}" class="btn btn-danger"><span class="fa fa-close"></span></a>
                    <a href="{{ route('searchlog.download', ['start' => request()->get('start'), 'end' => request()->get('end')]) }}" class="btn btn-success">Скачать</a>
                </span>
            </span>
        </form>

    </div>
    <hr>

    @if ($searchlog->count())
		<table class="table table-striped table-middle">
			<tbody>
				<tr>
					<th style="width: 10px">#</th>
					<th>IP</th>
					<th>Пользователь</th>
					<th>Дата</th>
					<th>Запрос</th>
				</tr>
				@foreach ($searchlog as $row)
					<tr>
						<td>{{ $row->id }}</td>
						<td>{{ $row->ip }}</td>
						<td>{{ $row->username }}</td>
						<td>{{ $row->added_at ? $row->added_at->format('Y-m-d H:i:s') : '' }}</td>
						<td>{{ $row->query }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>

        {!! $searchlog->render() !!}
    @else
        <div class="alert alert-warning">
            Нет данных
        </div>
	@endif
@endsection

@section('scripts')
@endsection
