@extends('layouts.admin')

@section('content')
	<p>
		<a href="{{ route('company_type.create') }}" class="btn btn-success">Создать профиль компании</a>
	</p>
	<hr>

		<table class="table table-striped">
			<tbody>
				<tr>
					<th style="width: 10px">#</th>
					<th>Название</th>
					<th width="140"></th>
				</tr>
				@if ($types->count())
					@foreach ($types as $type)
						<tr>
							<td>{{ $type->id }}</td>
							<td>{{ $type->name }}</td>
							<td>
								<a href="{{ route('company_type.edit' , $type->id) }}" class="btn btn-primary">
	                                <span class="fa fa-pencil"></span>
	                            </a>

	                            {!! Form::open(['route' => ['company_type.destroy', $type->id], 'method' => 'DELETE', 'class' => 'inline-block']) !!}
	                                <button type="submit" class="btn delete btn-danger">
	                                    <span class="fa fa-close"></span>
	                                </button>
	                            {!! Form::close() !!}
							</td>
						</tr>

                        @foreach ($type->childs as $child_type)
    						<tr>
    							<td>{{ $child_type->id }}</td>
    							<td> &ndash;&ndash;&ndash; {{ $child_type->name }}</td>
    							<td>
    								<a href="{{ route('company_type.edit' , $child_type->id) }}" class="btn btn-primary">
    	                                <span class="fa fa-pencil"></span>
    	                            </a>

    	                            {!! Form::open(['route' => ['company_type.destroy', $child_type->id], 'method' => 'DELETE', 'class' => 'inline-block']) !!}
    	                                <button type="submit" class="btn delete btn-danger">
    	                                    <span class="fa fa-close"></span>
    	                                </button>
    	                            {!! Form::close() !!}
    							</td>
    						</tr>
    					@endforeach
                    @endforeach
				@endif
			</tbody>
		</table>
		{{-- @if ($types->count())
			{!! $types->render() !!}
		@endif --}}
@endsection
