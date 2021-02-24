@extends('layouts.admin')

@section('content')
	<p>
		<form action="{{ route('subscriber.search') }}" class="col-md-8 pull-right">
			<span class="col-md-4">
				<a href="{{ route('subscriber.index', ['confirmed' => 1]) }}" class="btn btn-primary">email подтвержден</a>
				<a href="{{ route('subscriber.index', ['confirmed' => 0]) }}" class="btn btn-primary">email не подтвержден</a>
			</span>
			<span class="col-md-6">
				<input name="email" class="form-control search_by_email" placeholder="Поиск" value="{{ request()->get('email') }}">
			</span>
			<span class="col-md-2">
				<button type="submit" class="btn btn-primary"><span class="fa fa-search"></span></button>
				<a href="{{ route('subscriber.index') }}" class="btn btn-danger"><span class="fa fa-close"></span></a>
			</span>
		</form>

		<a href="{{ route('subscriber.download', ['confirmed' => request()->get('confirmed')]) }}" class="btn btn-success">Скачать список подписчиков</a>
	</p>
	<hr>

	<table class="table table-striped">
		<tbody>
			<tr>
				<th style="width: 10px">#</th>
				<th>Email</th>
				<th class="text-center">Подтвержден</th>
				<th>Дата создания</th>
				<th width="140"></th>
			</tr>
			@if ($subscribers->count())
				@foreach ($subscribers as $subscriber)
					<tr>
						<td>{{ $subscriber->id }}</td>
						<td>{{ $subscriber->email }}</td>
						<td class="text-center"><span class="fa {{ $subscriber->confirmed ? 'fa-check' : 'fa-close' }}"></span></td>
						<td>{{ $subscriber->created_at->format('Y-m-d H:i:s') }}</td>
						<td>
							{{-- <a href="{{ route('subscriber.edit' , $subscriber->id) }}" class="btn btn-primary">
                                <span class="fa fa-pencil"></span>
                            </a> --}}

                            {!! Form::open(['route' => ['subscriber.destroy', $subscriber->id], 'method' => 'DELETE', 'class' => 'inline-block']) !!}
                                <button type="submit" class="btn delete btn-danger">
                                    <span class="fa fa-close"></span>
                                </button>
                            {!! Form::close() !!}
						</td>
					</tr>
				@endforeach
			@endif
		</tbody>
	</table>
	@if ($subscribers->count())
		{!! $subscribers->appends(request()->query())->links() !!}
	@endif
@endsection
