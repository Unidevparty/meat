@extends('layouts.admin')

@section('content')
	 <table class="table table-striped">
		<tbody>
			<tr>
				<th style="width: 10px">#</th>
				<th>Дата</th>
				<th>Материал</th>
				<th>ID Материала</th>
				<th>Комментарий</th>
				<th class="text-center">Автор</th>
			</tr>
				@if ($comments->count())
					@foreach ($comments as $com)
						<tr>
							<td>{{ $com->id }}</td>
							<td>{{ $com->created_at ? $com->created_at->format('Y-m-d H:i:s') : '' }}</td>
							<td>{{ $com->type }}</td>
							<td>{{ $com->page_id }}</td>
							<td>{{ $com->text }}</td>
							<td>{{ $com->member_id }}</td>
																
													
						</tr>
					@endforeach
				@endif
		</tbody>
	</table>
@endsection