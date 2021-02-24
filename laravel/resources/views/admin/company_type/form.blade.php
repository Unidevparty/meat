@extends('layouts.admin')

@section('content')
	<div class="box-body pad">
		<div class="box">
			<div class="box-body">
				@if (!empty($type))
					{!! Form::model($type, ['route' => ['company_type.update', $type->id], 'method' => 'PATCH', 'enctype' => 'multipart/form-data']) !!}
			    @else
					{!! Form::open(['route' => 'company_type.store', 'enctype' => 'multipart/form-data']) !!}
			    @endif
					<div class="form-group{{ $errors->has('name') ? ' has-error' :'' }}">
					   <label for="name">Название</label>
					   {{ Form::text('name', empty($type) ? null : $type->name, ['class' => 'form-control', 'id' => 'name']) }}
					</div>

                    <div class="form-group{{ $errors->has('parent_id') ? ' has-error' :'' }}">
					    <label for="parent_id">Родитель</label>

                        <select class="form-control" name="parent_id">
                            <option value="0">Не указано</option>
                            @foreach ($company_types as $parent_type)
                                <option value="{{ $parent_type->id }}" {{ $parent_type->id == $type->id ? 'disabled' : '' }} {{ $parent_type->id == $type->parent_id ? 'selected' : '' }}>{{ $parent_type->name }}</option>

                                {{-- @if ($parent_type->childs)
                                    @foreach ($parent_type->childs as $child_type)
                                        <option value="{{ $child_type->id }}" disabled> &ndash;&ndash;&ndash; {{ $child_type->name }}</option>
                                    @endforeach
                                @endif --}}
                            @endforeach
                        </select>
					</div>


					<p>
						<a href="{{ route('company_type.index') }}" class="btn btn-danger">Отмена</a>
						<input type="submit" class="btn btn-success" value="Сохранить">
					</p>
			   {!! Form::close() !!}
			</div>
		</div>
	</div>
@endsection
