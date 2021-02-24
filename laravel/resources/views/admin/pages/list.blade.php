@extends('layouts.admin')

@section('content')
	<table class="table table-striped">
		<tbody>
			<tr>
				<th>Название</th>
				<th></th>
			</tr>
			@if ($files)
				@foreach ($files as $file)
					<tr>
						<td>{{ $file }}</td>
						<td>
							<a href="{{ route('pages.edit', ['file' => $file]) }}" class="btn btn-primary">
                                <span class="fa fa-pencil"></span>
                            </a>
						</td>
					</tr>
				@endforeach
			@endif

			<tr>
				<td>email.blade.php</td>
				<td>
					<a href="{{ route('pages.edit', ['file' => 'email.blade.php', 'path' => '../layouts/']) }}" class="btn btn-primary">
						<span class="fa fa-pencil"></span>
					</a>
				</td>
			</tr>
			<tr>
				<td>subscribe_confirm.blade.php</td>
				<td>
					<a href="{{ route('pages.edit', ['file' => 'subscribe_confirm.blade.php', 'path' => '../email/']) }}" class="btn btn-primary">
						<span class="fa fa-pencil"></span>
					</a>
				</td>
			</tr>
		</tbody>
	</table>
@endsection
