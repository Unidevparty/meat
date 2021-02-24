@extends('layouts.admin')

@section('content')
	<div class="box-body pad">
		<div class="box">
			<div class="box-body">
				@if (!empty($company))
					{!! Form::model($company, ['route' => ['company.update', $company->id], 'method' => 'PATCH', 'enctype' => 'multipart/form-data', 'id' => 'company_form']) !!}
			    @else
					{!! Form::open(['route' => 'company.store', 'enctype' => 'multipart/form-data', 'id' => 'company_form']) !!}
			    @endif
					<div class="form-group{{ $errors->has('name') ? ' has-error' :'' }}">
					   <label for="name">Название</label>
					   {{ Form::text('name', empty($company) ? null : $company->name, ['class' => 'form-control', 'id' => 'name']) }}
					</div>


					<div class="form-group{{ $errors->has('logo') ? ' has-error' :'' }}">
						<label for="logo">Логотип</label>
						<input type="file" name="logo" accept="image/x-png,image/gif,image/jpeg">
						@if (isset($company) && $company->logo)
							<p><img src="{{ $company->logo }}" width="100"></p>
						@endif
					</div>

					<div class="form-group{{ $errors->has('type') ? ' has-error' :'' }}">
					   <p><b>Тип компании</b></p>

					   	@foreach ($types as $type)
							<div class="radio">
								<label>
									<input type="radio" name="type" value="{{ $type }}" {{ isset($company) && $company->type == $type ? 'checked' : '' }}>
									{{ $type }}
								</label>
							</div>
					   	@endforeach
					</div>




                    <div class="form-group">
						<label>
							<input type="hidden" name="supplier" value="0">
							<input type="checkbox" name="supplier" class="minimal" value="1" {{ !empty($company) && $company->supplier ? 'checked' : '' }}>
							поставщик
						</label>
					</div>

                    <div class="form-group">
						<label>
							<input type="hidden" name="manufacturer" value="0">
							<input type="checkbox" name="manufacturer" class="minimal" value="1" {{ !empty($company) && $company->manufacturer ? 'checked' : '' }}>
							производитель
						</label>
					</div>

					<div class="form-group">
						<label for="types">Профиль компании:</label>
						{{-- {{ Form::select('types[]', $profiles, empty($company) ? null : $company->types()->pluck('name', 'name'), ['multiple' => true, 'class' => 'form-control', 'id' => 'types']) }} --}}


                        <select class="form-control multiple_select_strict" id="types" name="types[]" multiple>
                            @foreach ($profiles as $parent_type)
                                <optgroup label="{{ $parent_type->name }}">

                                    {{-- <option value="{{ $parent_type->id }}" {{ isset($current_profiles) && in_array($parent_type->id, $current_profiles) ? 'selected' : '' }}>{{ $parent_type->name }}</option> --}}

                                    @if ($parent_type->childs)
                                        @foreach ($parent_type->childs as $child_type)

                                            <option value="{{ $child_type->id }}" {{ isset($current_profiles) && in_array($child_type->id, $current_profiles) ? 'selected' : '' }}>{{ $child_type->name }}</option>

                                        @endforeach
                                    @endif
                                </optgroup>
                            @endforeach
                        </select>
					</div>

					<div class="form-group{{ $errors->has('year') ? ' has-error' :'' }}">
					   <label for="year">Год основания</label>
					   {{-- {{ Form::select('year', array_combine(range(date('Y'), 1950), range(date('Y'), 1950)), empty($company) ? null : $company->year, ['class' => 'form-control', 'id' => 'year']) }} --}}
					   {{ Form::text('year', empty($company) || !$company->year ? null : $company->year->format('Y-m-d H:i:s'), ['class' => 'form-control datetime_mask', 'id' => 'year']) }}
					</div>

					<div class="form-group">
						<input type="hidden" name="is_holding" value="0">
						<label id="is_holding">
							<input type="checkbox" name="is_holding" class="minimal" value="1" {{ !empty($company) && $company->is_holding ? 'checked' : '' }}>
							Это холдинг
						</label>
					</div>

					<div class="form-group">
						<label for="holding_id">Холдинг</label>
						@php
							$holding_id_attrs = [
								'class' => 'form-control',
								'id' => 'holding_id'
							];

							if ($company->is_holding) {
								$holding_id_attrs['disabled'] = $company->is_holding;
							}
						@endphp
						{{ Form::select('holding_id', $holdings, empty($company) ? null : $company->holding_id, $holding_id_attrs) }}
					</div>



					<div class="form-group">
					   <label for="title">Title  <small>(<span class="l">0</span> / 70)</small></label>
					   {{ Form::text('title', empty($company) ? null : $company->title, ['class' => 'form-control', 'id' => 'title', 'maxlength' => 70]) }}
					</div>

					<div class="form-group">
					   <label for="description">Description  <small>(<span class="l">0</span> / 140)</small></label>
					   {{ Form::text('description', empty($company) ? null : $company->description, ['class' => 'form-control', 'id' => 'description', 'maxlength' => 140, 'size' => 1400]) }}
					</div>

					<div class="form-group">
					   <label for="keywords">Keywords  <small>(<span class="l">0</span> / 140)</small></label>
					   {{ Form::text('keywords', empty($company) ? null : $company->keywords, ['class' => 'form-control', 'id' => 'keywords', 'maxlength' => 140]) }}
					</div>

					<div class="form-group">
					   <label for="text">Аннотация</label>
					   {{ Form::textarea('introtext', empty($company) ? null : $company->introtext, ['class' => 'form-control', 'rows' => 4]) }}
					</div>

					<div class="form-group">
					   <label for="text">Текст</label>
					   {{ Form::textarea('text', empty($company) ? null : $company->text, ['class' => 'form-control', 'id' => 'text', 'rows' => 2]) }}
					</div>











					<div class="form-group">
					   <label for="site">Сайт</label>
					   {{ Form::text('site', empty($company) ? null : $company->site, ['class' => 'form-control', 'id' => 'site']) }}
					</div>

					<div class="form-group">
					   <label for="facebook">Facebook</label>
					   {{ Form::text('facebook', empty($company) ? null : $company->facebook, ['class' => 'form-control', 'id' => 'facebook']) }}
					</div>

					<div class="form-group">
					   <label for="google_plus">google+</label>
					   {{ Form::text('google_plus', empty($company) ? null : $company->google_plus, ['class' => 'form-control', 'id' => 'google_plus']) }}
					</div>

					<div class="form-group">
					   <label for="vk">Вконтакте</label>
					   {{ Form::text('vk', empty($company) ? null : $company->vk, ['class' => 'form-control', 'id' => 'vk']) }}
					</div>

					<div class="form-group">
					   <label for="instagram">Instagram</label>
					   {{ Form::text('instagram', empty($company) ? null : $company->instagram, ['class' => 'form-control', 'id' => 'instagram']) }}
					</div>

					{{-- <div class="form-group">
					    <label for="phone">Номер телефона</label>
					    {{ Form::text('phone', empty($company) ? null : $company->phone, ['class' => 'form-control', 'id' => 'phone']) }}
                    </div> --}}

                    <div class="form-group">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Номер телефона</th>
                                    <th>Комментарий</th>
                                    <th class="multiple-btn-wrap text-right" width="50">
                                        <button type="button" class="btn btn-primary add-multiple-row"><i class="fa fa-plus"></i></button>
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @if ($company->phone)
                                    @foreach ($company->phone as $phone)
                                        <tr>
                                            <td>
                                                {!! Form::text('phone[number][]', $phone->number, ['class' => 'form-control']) !!}
                                            </td>

                                            <td>
                                                {!! Form::text('phone[description][]', $phone->description, ['class' => 'form-control']) !!}
                                            </td>

                                            <td class="multiple-btn-wrap text-right">
                                                <button class="btn btn-danger remove-multiple-row" type="button"><i class="fa fa-close"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                <tr>
                                    <td>
                                        {!! Form::text('phone[number][]', null, ['class' => 'form-control']) !!}
                                    </td>

                                    <td>
                                        {!! Form::text('phone[description][]', null, ['class' => 'form-control']) !!}
                                    </td>

                                    <td class="multiple-btn-wrap text-right">
                                        <button class="btn btn-danger remove-multiple-row " type="button"><i class="fa fa-close"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>


					<div class="form-group">
					   <label for="email">Email</label>
					   {{ Form::text('email', empty($company) ? null : $company->email, ['class' => 'form-control', 'id' => 'email']) }}
					</div>


					<div class="form-group">
					   <label for="manager_email">Email для уведомлений</label>
					   {{ Form::text('manager_email', empty($company) ? null : $company->manager_email, ['class' => 'form-control', 'id' => 'manager_email']) }}
					</div>

					<div class="form-group">
					   <label for="address">Адрес</label>
					   {{ Form::text('address', empty($company) ? null : $company->address, ['class' => 'form-control', 'id' => 'address', 'autocomplete' => 'off']) }}
					   <div class="autocomplete">
						   <div class="options"></div>
					   </div>

					    <div class="row">
							<div class="col-sm-2">Координаты</div>
						   	<div class="col-sm-10">{{ Form::text('coords', empty($company) ? null : $company->coords, ['class' => 'form-control', 'id' => 'coords']) }}</div>
					    </div>
						<div class="row">
    						<div class="col-sm-2">Страна</div>
							<div class="col-sm-10">{{ Form::text('country', empty($company) ? null : $company->country, ['class' => 'form-control', 'id' => 'country']) }}</div>
						</div>
						<div class="row">
    						<div class="col-sm-2">Регион</div>
							<div class="col-sm-10">{{ Form::text('region', empty($company) ? null : $company->region, ['class' => 'form-control', 'id' => 'region']) }}</div>
						</div>
						<div class="row">
    						<div class="col-sm-2">Нас пункт</div>
							<div class="col-sm-10">{{ Form::text('city', empty($company) ? null : $company->city, ['class' => 'form-control', 'id' => 'city']) }}</div>
					   </div>
					</div>









{{--
					<div class="gallery">
						<h3>Фотогалерея</h3>
						<table class="table table-middle">
							<tr>
								<th width="90">Изображение</th>
								<th></th>
								<th>Описание</th>
								<th><a href="#" class="add_gallery_row btn btn-primary btn-sm"><i class="fa fa-plus"></i></a></th>
							</tr>
							@if ($company->gallery)
								@foreach ($company->gallery as $id => $photo)
									@include('admin.company.gallery_row', [
										'id' => $id,
										'photo' => $photo
									])
								@endforeach
							@endif
							@include('admin.company.gallery_row', [
								'id' => $id + 1,
								'photo' => null
							])
						</table>
					</div> --}}


                    <div class="gallery_wrap">
						<h3>Фотографии</h3>

            			<p><input type="file" id="photos" name="gallery_photos[]" multiple/></p>

						<div class="gallery_container">
							@if ($company->gallery)
									@foreach ($company->gallery as $id => $photo)
										@include('admin.photogallery.photo_row', [
											'id' => $id,
											'photo' => $photo
										])
									@endforeach
							@endif
						</div>

						<div class="proto hidden">
							@include('admin.photogallery.photo_row', [
								'id' => $id + 1,
								'photo' => null
							])
						</div>
					</div>


					<div class="brand">
						<h3>Торговые марки</h3>
						<table class="table table-middle">
							<tr>
								<th width="90">Изображение</th>
								<th></th>
								<th>Описание</th>
								<th><a href="#" class="add_brand_row btn btn-primary btn-sm"><i class="fa fa-plus"></i></a></th>
							</tr>
							@if ($company->brands)
								@foreach ($company->brands as $id => $photo)
									@include('admin.company.brand_row', [
										'id' => $id,
										'photo' => $photo
									])
								@endforeach
							@endif
							@include('admin.company.brand_row', [
								'id' => $id + 1,
								'photo' => null
							])
						</table>
					</div>

					<div class="videos">
						<h3>Видео</h3>
						<table class="table table-middle">
							<tr>
								<th width="90">Изображение</th>
								<th></th>
								<th>Ссылка</th>
								<th>Описание</th>
								<th><a href="#" class="add_video_row btn btn-primary btn-sm"><i class="fa fa-plus"></i></a></th>
							</tr>
							@if ($company->videos)
								@foreach ($company->videos as $id => $video)
									@include('admin.company.video_row', [
										'id' => $id,
										'video' => $video
									])
								@endforeach
							@endif
							@include('admin.company.video_row', [
								'id' => $id + 1,
								'video' => null
							])
						</table>
					</div>

					<div class="files">
						<h3>Файлы</h3>
						<table class="table table-middle">
							<tr>
								<th>Файл</th>
								<th>Описание</th>
								<th><a href="#" class="add_file_row btn btn-primary btn-sm"><i class="fa fa-plus"></i></a></th>
							</tr>
							@if ($company->files)
								@foreach ($company->files as $id => $file)
									@include('admin.company.file_row', [
										'id' => $id,
										'file' => $file
									])
								@endforeach
							@endif
							@include('admin.company.file_row', [
								'id' => $id + 1,
								'file' => null
							])
						</table>
					</div>



					<div class="form-group">
					   <label for="published_at">Дата публикации</label>
					   {{ Form::text('published_at', empty($company) || !$company->published_at ? Carbon\Carbon::now()->format('Y-m-d H:i:s') : $company->published_at->format('Y-m-d H:i:s'), ['class' => 'form-control datetime_mask', 'id' => 'published_at']) }}
					</div>

					<div class="form-group">
						<label>
							<input type="hidden" name="published" value="0">
							<input type="checkbox" name="published" class="minimal" value="1" {{ !empty($company) && $company->published ? 'checked' : '' }}>
							опубликовано
						</label>
					</div>

					@if (!empty($company))
						<p>Просмотры: <b>{{ $company->views }}</b></p>
					@endif

					<p>
						<a href="{{ route('company.index') }}" class="btn btn-danger">Отмена</a>
						<input type="submit" class="btn btn-success" value="Сохранить">

						@if (!empty($company))
							<a href="{{ route('company.show' , $company->alias) }}" target="_blank" class="btn btn-primary">
								<span class="fa fa-eye"></span> Перейти на страницу
							</a>
						@endif
					</p>
			   {!! Form::close() !!}
			</div>
		</div>
	</div>
@endsection

@section('scripts')
    <script src="/assets/admin_assets/plugins/jquery-sortable.js" charset="utf-8"></script>
	<script src="/vendor/laravel-filemanager/js/lfm.js"></script>
	<script src="/assets/admin_assets/js/geocoder.js"></script>
	<script src="/assets/admin_assets/js/company.js" charset="utf-8"></script>
@endsection
