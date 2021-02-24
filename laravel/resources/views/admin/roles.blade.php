@extends('layouts.admin')

@section('content')
	<div class="box-body pad">
		<div class="box">
			<div class="box-body">
				{!! Form::open(['route' => 'settings.roles.save', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) !!}
					<table class="table table-bordered">
						<tr>
							<th></th>
							@foreach ($roles as $role)
								<th class="text-center">{{ $role }}</th>
							@endforeach
						</tr>
						@foreach ($rules as $key => $rule)
							<tr>
								<th>{{ $rule }}</th>
								@foreach ($roles as $role_key => $role)
									<td class="text-center">
										<?php
											$checked = !empty($saved_roles[$key][$role_key]) || $role_key == 4;
										?>
										<input type="hidden" name="data[{{ $key }}][{{ $role_key }}]" value="{{ $role_key == 4 ? '1' : '0' }}">
										<input type="checkbox" name="data[{{ $key }}][{{ $role_key }}]" value="1" class="minimal" {{ $checked ? 'checked' : '' }} {{ $role_key == 4 ? 'disabled' : '' }}>
									</td>
								@endforeach
							</tr>
						@endforeach
					</table>
					<p>
						<input type="submit" class="btn btn-success" value="Сохранить">
					</p>
			   {!! Form::close() !!}
			</div>
		</div>
	</div>
@endsection