@extends('layouts.admin')

@section('content')
	<div class="box-body pad">
		<div class="box">
			<div class="box-body">
				<h3>Привет {{ $user->name }}</h3>

				<p>Страница находится в разработке</p>

				{!! $content !!}
			</div>
		</div>
	</div>
@endsection
