@extends('layouts.admin')

@section('content')
	<div class="box-body pad">
		<div class="box">
			<div class="box-body">
				@if (!empty($companies_filter))
					{!! Form::model($companies_filter, ['route' => ['companies_filter.update', $companies_filter->id], 'method' => 'PATCH', 'enctype' => 'multipart/form-data']) !!}
			    @else
					{!! Form::open(['route' => 'companies_filter.store', 'enctype' => 'multipart/form-data']) !!}
			    @endif
					<div class="form-group{{ $errors->has('name') ? ' has-error' :'' }}">
					   <label for="name">Название</label>
					   {{ Form::text('name', empty($companies_filter) ? null : $companies_filter->name, ['class' => 'form-control', 'id' => 'name']) }}
					</div>

                    <div class="form-group">
						<label for="profiles">Профиль компании:</label>

                        <select class="form-control multiple_select_strict" id="profiles" name="profiles[]" multiple>
                            @foreach ($profiles as $parent_type)
                                <optgroup label="{{ $parent_type->name }}">@if ($parent_type->childs)
                                        @foreach ($parent_type->childs as $child_type)
                                            <option value="{{ $child_type->id }}" {{ isset($companies_filter) && in_array($child_type->id, $companies_filter->profiles) ? 'selected' : '' }}>{{ $child_type->name }}</option>
                                        @endforeach
                                    @endif
                                </optgroup>
                            @endforeach
                        </select>
					</div>

                    <div class="form-group">
						<label for="country">Страна:</label>

                        <select class="form-control" id="country" name="country">
                            @foreach ($countries_map as $country => $regions)
                                <option value="{{ $country }}" {{ isset($companies_filter) && $country == $companies_filter->country ? 'selected' : '' }}>{{ $country ? $country : 'Не указано' }}</option>
                            @endforeach
                        </select>
					</div>

                    @php
                        $regions = null;

                        if (isset($companies_filter) && isset($countries_map[$companies_filter->country])) {
                            $regions = $countries_map[$companies_filter->country];
                        } else {
                            $regions = array_shift($countries_map);
                        }
                    @endphp

                    <div class="form-group">
						<label for="region">Регион:</label>

                        <select class="form-control" id="region" name="region">
                            @foreach ($regions as $region => $cities)
                                <option value="{{ $region }}" {{ isset($companies_filter) && $region == $companies_filter->region ? 'selected' : '' }}>{{ $region ? $region : 'Не указано' }}</option>
                            @endforeach
                        </select>
					</div>

                    @php
                        $cities = null;

                        if (isset($companies_filter) && isset($regions[$companies_filter->region])) {
                            $cities = $regions[$companies_filter->region];
                        } else {
                            $cities = array_shift($regions);
                        }
                    @endphp

                    <div class="form-group">
						<label for="city">Населенный пункт:</label>

                        <select class="form-control" id="city" name="city">
                            @foreach ($cities as $city => $city_companies)
                                <option value="{{ $city }}" {{ isset($companies_filter) && $city == $companies_filter->city ? 'selected' : '' }}>{{ $city ? $city : 'Не указано' }}</option>
                            @endforeach
                        </select>
					</div>

                    <div class="form-group">
                        <p><b>Тип фильтра</b></p>

                        <div class="radio">
                            <label>
                                <input type="radio" name="type" value="top" {{ isset($companies_filter) && $companies_filter->type == 'top' ? 'checked' : 'checked' }}>
                                Топ запросов
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="type" value="block_with_numbers" {{ isset($companies_filter) && $companies_filter->type == 'block_with_numbers' ? 'checked' : '' }}>
                                Блок с цифрами
                            </label>
                        </div>
                    </div>


					<p>
						<a href="{{ route('companies_filter.index') }}" class="btn btn-danger">Отмена</a>
						<input type="submit" class="btn btn-success" value="Сохранить">
					</p>
			   {!! Form::close() !!}
			</div>
		</div>
	</div>
@endsection



@section('scripts')
	<script type="text/javascript">
        var countries = [
            @foreach ($countries_map as $country => $regions)
                {
                    name: "{{ $country or 'Не указано' }}",
                    value: "{{ $country or '' }}",
                    cities: [],
                    regions: [
                        @foreach ($regions as $region => $cities)
                            {
                                name: "{{ $region or 'Не указано' }}",
                                value: "{{ $region or '' }}",
                                cities:[
                                    @foreach ($cities as $city => $city_companies)
                                        {
                                            name:  "{{ $city or 'Не указано' }}",
                                            value: "{{ $city or '' }}",
                                            count: {{ $city_companies }}
                                        },
                                    @endforeach
                                ]
                            },
                        @endforeach
                    ]
                },
            @endforeach
        ];


        $('#country').change(function() {
            var country = $(this).val();

            build_region_options(country);
            build_city_options(country);
        });

        $('#region').change(function() {
            var country = $('#country').val();
            var region = $(this).val();

            build_city_options(country, region);
        });

        function getRegionsByCountry(country) {
            for (var i = 0; i < countries.length; i++) {
                if (countries[i].value == country) {
                    return countries[i].regions;
                }
            }
        }

        function getCitiesByRegion(country, region) {
            var regions = getRegionsByCountry(country);

            for (var i = 0; i < regions.length; i++) {
                if (regions[i].value == region) {
                    return regions[i].cities;
                }
            }
        }


        function build_region_options(country) {
            var options = '';
            var regions = getRegionsByCountry(country);

            for (var j = 0; j < regions.length; j++) {
                options += '<option value="' + regions[j].value + '">' + regions[j].name + '</option>';
            }

            $('#region').html(options);
        }

        function build_city_options(country, region) {
            var options = '';

            if (!region) {
                region = getRegionsByCountry(country)[0].name;
            }

            var cities = getCitiesByRegion(country, region);

            for (var j = 0; j < cities.length; j++) {
                options += '<option value="' + cities[j].value + '">' + cities[j].name + '</option>';
            }

            $('#city').html(options);
        }
	</script>
@endsection
