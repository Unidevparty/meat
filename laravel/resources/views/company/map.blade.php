@section('top_content')
	<div class="map-section">
		<div class="map-itself" id="map">
		</div>
		<div class="map-stats">
			<div class="nothing-found">
				<i class="icon-nothing"></i>
				<span>по запросу ничего не найдено</span>
			</div>
			<a href="" class="clear-filter">
				Сбросить Фильтр
				<i class="icon-clear-filter"></i>
			</a>
		</div>

		<div class="width">
			<div class="map-filter">
				<div class="map-filter-callers" id="mapFilter">
					<div class="filter-caller" data-mapfilter-call="01" data-placeholder="Профиль компании" data-filter="companyType" data-filter-value=""><span>Профиль компании</span></div>
					<div class="filter-caller disabled" data-mapfilter-call="02" data-placeholder="Страна" data-filter="country" data-filter-value=""><span>Страна</span></div>
					<div class="filter-caller disabled" data-mapfilter-call="03" data-placeholder="Регион" data-filter="region" data-filter-value=""><span>Регион</span></div>
					<div class="filter-caller disabled" data-mapfilter-call="04" data-placeholder="населенный пункт" data-filter="city" data-filter-value=""><span>населенный пункт</span></div>
					<button id="callFilter">
						<i class="icon icon-search mob-hide"></i>
						<span class="mob-show">Поиск</span>
					</button>
				</div>
				<div class="map-filter-body">
					<div class="map-filter-tab map-filter-tab-index" data-mapfilter="00">
						<div class="section hl">
							<ul class="map-filter-list map-filter-shortcuts">
								<li data-mapfilter-call="02"><strong>{{ $countries_count }}</strong> <span>Стран</span></li>
								<li data-mapfilter-call="01"><strong class="map_companies_count">{{ $companies_count }}</strong> <span>Компаний</span></li>
								<li data-mapfilter-call="01">
                                    <a href="{{ route('company.list', ['suppliers' => 1]) }}">
                                        <strong>{{ $suppliers_count }}</strong> <span>Поставщиков</span>
                                    </a>
                                </li>
								<li data-mapfilter-call="01">
                                    <a href="{{ route('company.list', ['suppliers' => 1]) }}">
                                        <strong>{{ $manufacturers_count }}</strong> <span>Производителей</span>
                                    </a>
                                </li>
							</ul>
						</div>
						<div class="section">
							<ul class="map-filter-list map-filter-countries">
                                @foreach ($company_filters as $company_filter)
                                    @if ($company_filter->type == 'block_with_numbers')
                                        <li>
                                            <a href="?{!! $company_filter->filter_string !!}" data-profiles="{{ implode(',', $company_filter->profiles) }}" data-country="{{ $company_filter->country }}" data-region="{{ $company_filter->region }}" data-city="{{ $company_filter->city }}">
                                                <strong>{{ $company_filter->companiesCount }}</strong>
                                                <span>{{ $company_filter->name }}</span>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
							</ul>
						</div>
						<div class="section">
							<div class="h">ТОП запросов:</div>
							<ul class="map-filter-list map-filter-regions">
                                @foreach ($company_filters as $company_filter)
                                    @if ($company_filter->type == 'top')
                                        <li>
                                            <a href="?{!! $company_filter->filter_string !!}" data-profiles="{{ implode(',', $company_filter->profiles) }}" data-country="{{ $company_filter->country }}" data-region="{{ $company_filter->region }}" data-city="{{ $company_filter->city }}">{{ $company_filter->name }}</a>
                                        </li>
                                    @endif
                                @endforeach
							</ul>
						</div>
					</div>
					<div class="map-filter-tab" data-mapfilter="01" data-filter-multiple>
						<div class="mb-scroll-cnt">
							<div class="h mob-show">Профили</div>
							<ul class="map-filter-list map-filter-types">
                                {{-- <li v-for="main_profile in profiles" v-if="main_profile.companies">
                                    <div class="h">
                                        <a href="" :data-filter-content="main_profile.name" :class="[ main_profile.companies ? '' : 'disabled']">@{{ main_profile.name }} <span class="qnt">(@{{ main_profile.companies }})</span></a>
                                    </div>
                                    <ul>
                                        <li v-for="profile in main_profile.profiles" v-if="profile.companies">
                                            <a href="" :data-filter-content="profile.name" :class="[ profile.companies ? '' : 'disabled']" @click="checkProfile">@{{ profile.name }} <span class="qnt">(@{{ profile.companies }})</span></a>
                                        </li>
                                    </ul>
                                </li> --}}


                                @foreach ($profiles as $main_profile)
                                    <li>
                                        <div class="h"><a href="" data-filter-content="{{ $main_profile['name'] }}" @if ($main_profile['companies']==0) class="disabled" @endif>{{ $main_profile['name'] }} <span class="qnt">({{ $main_profile['companies'] }})</span></a></div>
                                        <ul>
                                            @foreach ($main_profile['profiles'] as $profile)
                                                <li><a href="" data-filter-content="{{ $profile['name'] }}" @if ($profile['companies']==0) class="disabled" @endif>{{ $profile['name'] }} <span class="qnt">({{ $profile['companies'] }})</span></a></li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @endforeach
							</ul>
						</div>
						<a href="" class="close"></a>
					</div>

					<div class="map-filter-tab" data-mapfilter="02">
						<div class="mb-scroll-cnt">
						<div class="h">Страны ({{ count($countries_list) }})</div>
    						<ul class="map-filter-list map-filter-countries">
                                @foreach ($countries_list as $country)
                                    <li><a href="" data-filter-content="{{ $country['country'] or '' }}" @if ($country['cnt']==0) class="disabled" @endif><strong>{{ $country['cnt'] }}</strong><span>{{ $country['country'] or 'Не указано' }}</span></a></li>
                                @endforeach
    						</ul>
						</div>
						<a href="" class="close"></a>

					</div>
					<div class="map-filter-tab" data-mapfilter="03">
						<div class="mb-scroll-cnt">

						<div class="h">Регионы</div>
    						<ul class="map-filter-list map-filter-regions">
                                @foreach ($region_list as $region)
                                    <li><a href="" data-filter-content="{{ $region['region'] or '' }}" @if ($region['cnt']==0) class="disabled" @endif>{{ $region['region'] or 'Не указано' }} <span class="qnt">({{ $region['cnt'] }})</span></a></li>
                                @endforeach
    						</ul>
						</div>
						<a href="" class="close"></a>

					</div>
					<div class="map-filter-tab" data-mapfilter="04">
						<div class="mb-scroll-cnt">

						<div class="h">Города ЦФО <span class="qnt"></span></div>
						<ul class="map-filter-list map-filter-regions">

						</ul>
						</div>
						<a href="" class="close"></a>

					</div>
				</div>
			</div>
		</div>

	</div>
	<div class="mob-show">
		<div class="mobile-map-comment">
			<i></i>
			Используйте 2 пальца чтоб изменять масштаб и перемещать карту
		</div>
	</div>

@endsection
