@extends('layouts.admin')

@section('content')
	<div class="button text-right">
		{!! Form::open(['route' => ['download.save', $article->id], 'method' => 'post', 'class' => 'inline-block']) !!}
		<input type="hidden" name="category" value="articles" />
		<input type="hidden" name="published" value="{{ $article->published_at->format('Y-m-d H:i:s') }}" />
		<input type="hidden" name="url" value="{{ route('articles.show' , $article->alias) }}" />
		<button type="submit" class="btn btn-primary mb-15">
			<span class="fa fa-save"></span>
			Выгрузить в Excel
		</button>
		{!! Form::close() !!}
	</div>

	<div class="col-md-6">
		<div class="text-left">
			<a href="{{ route('articles.show' , $article->alias) }}">{{ $article->name }}</a>
		</div>
	</div>
	<div class="col-md-6">
		<div class="text-right">
			<b>Дата публикации:</b> {{ $article->published_at->format('Y-m-d H:i:s') }}
		</div>
	</div>

	<table class="table table-bordered">
		<tbody>
			<tr>
				<td colspan="5" class="text-center">Общая статистика</td>
			</tr>
			<tr>
				<td>Дочитывание</td>
				<td class="text-right">25%</td>
				<td class="text-right">50%</td>
				<td class="text-right">75%</td>
				<td class="text-right">100%</td>
			</tr>
			<tr>
				<td>Общее кол-во</td>
				@foreach ($data as $percent => $item)
					<td class="text-right">{{ $percentArray[$percent]['views'] }}</td>
				@endforeach
			</tr>
			<tr>
				<td>В процентах</td>
				@foreach ($data as $percent => $item)
					<td class="text-right">{{ $percentArray[$percent]['percent'] }}</td>
				@endforeach
			</tr>
		</tbody>
	</table>

	@foreach ($data as $percent => $items)

		@if (!empty($items))

			@php
				$total = count($items);
			@endphp

			<div class="item">
			<div class="title text-center"><b>{{ $percent }}</b></div>
			<table class="table table-bordered">
				<thead class="thead-dark">
					<tr>
						<th>Дата</th>
						<th>Имя</th>
						<th>Группа</th>
						<th>Номер визита</th>
						<th>IP</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($items as $value)
						<tr>
							<td>{{ $value['date'] }}</td>
							<td>{{ $value['name'] }}</td>
							<td>{{ $value['group'] }}</td>
							<td class="text-right">{{ $value['views'] }}</td>
							<td >{{ $value['ip'] }}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
			<div class="text-center mb-15">Всего: {{ $total }}</div>

			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Список групп пользователей</th>
						<th>Кол-во</th>
						<th>% от общего в данной группе</th>
					</tr>
				</thead>
				<tbody>
				@foreach ($group_percent[$percent] as $group => $count)
					<tr>
						<td>{{ $group }}</td>
						<td class="text-right">{{ $percentArray[$percent][$group] }}</td>
						<td class="text-right">{{ $percentArray[$percent]['group_percent'] }}</td>
					</tr>
				@endforeach
				</tbody>
			</table>
			<div class="text-center mb-15">Всего: {{ $total }}</div>
		</div>

		@endif
	@endforeach

	<div class="text-center mt-3">Общая статистика</div>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Список групп пользователей</th>
				<th>Кол-во</th>
				<th>% от общего в данной группе</th>
			</tr>
		</thead>
		<tbody>
		@php
			$total_result = 0;
		@endphp
		@foreach ($allGroups as $name => $value)
			<tr>
				<td>{{ $name }}</td>
				<td class="text-right">{{ $value['count'] }}</td>
				<td class="text-right">{{ $value['group_percent'] }}</td>
			</tr>
			@php
				$total_result += $value['count'];
			@endphp
		@endforeach
		</tbody>
	</table>
	<div class="text-center">Всего: {{ $total_result }}</div>

<style type="text/css">
	.mb-15{
		margin-bottom: 15px;
	}
	th {
		color: #fff;
		background-color: #212529;
		border-color: #32383e;
	}
	td, th {
		border: 1px solid #00000040 !important;
		padding: 4px;
	}
</style>
@endsection
