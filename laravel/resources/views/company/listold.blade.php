@extends('layouts.main')

@section('page_content')

	@php
		$banner_a3 = getBanner('A-3');
		$banner_b1 = getBanner('B-1');
		$banner_b2 = getBanner('B-2');
		$banner_h1 = getBanner('H-1');
		$banner_k  = getBanner('K');
		$banner_c1 = getBanner('C-1');
		$banner_c2 = getBanner('C-2');
		$banner_p1 = getBanner('P-1');
		$banner_t1 = getBanner('T-1');
		$banner_t2 = getBanner('T-2');
	@endphp

    <div class="content-row">
    	<div class="section-header">
    		<h1 class="h">Каталог компаний</h1>
    	</div>
    	<div class="content-cells">
    		<div class="content-cell content-cell-main">
    			<div class="content-section">
    				<div class="filter-block">
    					<form action="" method="get" class="filter">
    						<div class="filter-cell">
    							<select class="custom-select" name="holding">
									<option value="">холдинг не указан</option>

									@foreach ($holdings as $id => $holding)
										<option value="{{ $id }}" {{ $id == $current_holding ? 'selected' : '' }}>{{ $holding }}</option>
									@endforeach
    							</select>
    						</div>
    						<div class="filter-cell">
    							<select class="custom-select" name="profile">
									<option value="">профиль не указан</option>

									@foreach ($profiles as $id => $profile)
										<option value="{{ $id }}" {{ $id == $current_profile ? 'selected' : '' }}>{{ $profile }}</option>
									@endforeach
    							</select>
    						</div>
    						<div class="filter-cell">
    							<select class="custom-select" name="date">
									<option value="">дата не указана</option>

									@foreach ($dates as $date)
										<option value="{{ $date }}" {{ $date == $current_date ? 'selected' : '' }}>{{ $date }}</option>
									@endforeach
    							</select>
    						</div>
    					</form>
    				</div>


					<?php $companies2 = $companies->splice(2); ?>

					@foreach ($companies as $company)
						@include('company.thumb', ['company' => $company])
					@endforeach

    			</div>
    		</div>
    		<div class="content-cell content-cell-aside mob-hide">
    			@include('partials.subscribe_side_form')

    			<div class="side-box">
    				@include('partials.more_news')
    			</div>
    		</div>
    	</div>
    </div>


    <div class="content-row banners-row">
    	<div class="content-cells">
            @if (!empty($banner_b1))
				<div class="content-cell content-cell-6 content-cell-tab-12 mob-hide">
					<a href="{{ $banner_b1->fake_url }}" target="_blank" rel="nofollow">
						<img src="{{ $banner_b1->main_image }}" alt="{{ $banner_b1->name }}">
					</a>
				</div>
			@endif
			@if (!empty($banner_b2))
				<div class="content-cell content-cell-6 content-cell-tab-12 mob-hide">
					<a href="{{ $banner_b2->fake_url }}" target="_blank" rel="nofollow">
						<img src="{{ $banner_b2->main_image }}" alt="{{ $banner_b2->name }}">
					</a>
				</div>
			@endif
            @if (!empty($banner_b1))
				<div class="mob-additional-banner nobefore">
					<a href="{{ $banner_b1->fake_url }}" target="_blank" rel="nofollow">
						<img src="{{ $banner_b1->mobile_image }}" alt="{{ $banner_b1->name }}">
					</a>
				</div>
			@endif
			@if (!empty($banner_b2))
				<div class="mob-additional-banner nobefore">
					<a href="{{ $banner_b2->fake_url }}" target="_blank" rel="nofollow">
						<img src="{{ $banner_b2->mobile_image }}" alt="{{ $banner_b2->name }}">
					</a>
				</div>
			@endif
    	</div>
    </div>

    @if (!empty($banner_a3))
		<div class="content-row banner-through tab-gea">
			<a href="{{ $banner_a3->fake_url }}" target="_blank" rel="nofollow">
				<img src="{{ $banner_a3->tablet_image }}" alt="{{ $banner_a3->name }}">
			</a>
		</div>
	@endif



    <div class="content-row">
    	<div class="content-cells">
    		<div class="content-cell content-cell-main">
    			<div class="content-section">
					<?php $companies3 = $companies2->splice(3); ?>
					@foreach ($companies2 as $company)
						@include('company.thumb', ['company' => $company])
					@endforeach
    			</div>

                @if (!empty($banner_k))
					<div class="mob-additional-banner">
						<a href="{{ $banner_k->fake_url }}" target="_blank" rel="nofollow">
							<img src="{{ $banner_k->mobile_image }}" alt="{{ $banner_k->name }}">
						</a>
					</div>
				@endif
                @if (!empty($banner_h1))
					<div class="content-section banner mob-hide">
						<a href="{{ $banner_h1->fake_url }}" target="_blank" rel="nofollow">
							<img src="{{ $banner_h1->main_image }}" alt="{{ $banner_h1->name }}">
						</a>
					</div>
				@endif
                @if (!empty($banner_h1))
					<div class="mob-additional-banner nobefore">
						<a href="{{ $banner_h1->fake_url }}" target="_blank" rel="nofollow">
							<img src="{{ $banner_h1->mobile_image }}" alt="{{ $banner_h1->name }}">
						</a>
					</div>
				@endif
				@if (!empty($banner_a3))
					<div class="content-section banner lap-hide">
						<a href="{{ $banner_a3->fake_url }}" target="_blank" rel="nofollow">
							<img src="{{ $banner_a3->mobile_image }}" alt="{{ $banner_a3->name }}">
						</a>
					</div>
				@endif

    			<div class="content-section">
					<?php $companies4 = $companies2->splice(1); ?>
					@foreach ($companies3 as $company)
						@include('company.thumb', ['company' => $company])
					@endforeach

    				<div class="mob-additional-banner">
                        @if (!empty($banner_c1))
							<a href="{{ $banner_c1->fake_url }}" target="_blank" rel="nofollow">
								<img src="{{ $banner_c1->mobile_image }}" alt="{{ $banner_c1->name }}">
							</a>
						@endif
						@if (!empty($banner_c2))
							<a href="{{ $banner_c2->fake_url }}" target="_blank" rel="nofollow">
								<img src="{{ $banner_c2->mobile_image }}" alt="{{ $banner_c2->name }}">
							</a>
						@endif
    				</div>

					<?php $companies5 = $companies4->splice(2); ?>
					@foreach ($companies4 as $company)
						@include('company.thumb', ['company' => $company])
					@endforeach

                    @if (!empty($banner_t1) || !empty($banner_t2))
						<div class="mob-additional-banner">
							@if (!empty($banner_t1))
								<a href="{{ $banner_t1->fake_url }}" target="_blank" rel="nofollow">
									<img src="{{ $banner_t1->mobile_image }}" alt="{{ $banner_t1->name }}">
								</a>
							@endif
							@if (!empty($banner_t2))
								<a href="{{ $banner_t2->fake_url }}" target="_blank" rel="nofollow">
									<img src="{{ $banner_t2->mobile_image }}" alt="{{ $banner_t2->name }}">
								</a>
							@endif
						</div>
					@endif

					<?php $companies6 = $companies5->splice(2); ?>
					@foreach ($companies5 as $company)
						@include('company.thumb', ['company' => $company])
					@endforeach

                    @if (!empty($banner_p))
                        <div class="mob-additional-banner">
							<a href="{{ $banner_p->fake_url }}" target="_blank" rel="nofollow">
								<img src="{{ $banner_p->mobile_image }}" alt="{{ $banner_p->name }}">
							</a>
        				</div>
                    @endif

					@foreach ($companies6 as $company)
						@include('company.thumb', ['company' => $company])
					@endforeach

                    @if ($more_link)
						<a href="{{ $more_link }}" class="btn load_more btn-reg btn-red wide">загрузить еще</a>
					@endif

                    <div class="load_more_before"></div>
    			</div>
    		</div>
    		<div class="content-cell content-cell-aside mob-hide">
                @if (!empty($banner_k))
					<a href="{{ $banner_k->fake_url }}" class="banner" target="_blank" rel="nofollow">
						<img src="{{ $banner_k->main_image }}" alt="{{ $banner_k->name }}">
					</a>
				@endif
				@if (!empty($banner_c1))
					<a href="{{ $banner_c1->fake_url }}" class="banner" target="_blank" rel="nofollow">
						<img src="{{ $banner_c1->main_image }}" alt="{{ $banner_c1->name }}">
					</a>
				@endif
				@if (!empty($banner_c2))
					<a href="{{ $banner_c2->fake_url }}" class="banner" target="_blank" rel="nofollow">
						<img src="{{ $banner_c2->main_image }}" alt="{{ $banner_c2->name }}">
					</a>
				@endif
				@if (!empty($banner_p1))
					<a href="{{ $banner_p1->fake_url }}" class="banner" target="_blank" rel="nofollow">
						<img src="{{ $banner_p1->main_image }}" alt="{{ $banner_p1->name }}">
					</a>
				@endif
				@if (!empty($banner_t1))
					<a href="{{ $banner_t1->fake_url }}" class="banner" target="_blank" rel="nofollow">
						<img src="{{ $banner_t1->main_image }}" alt="{{ $banner_t1->name }}">
					</a>
				@endif
				@if (!empty($banner_t2))
					<a href="{{ $banner_t2->fake_url }}" class="banner" target="_blank" rel="nofollow">
						<img src="{{ $banner_t2->main_image }}" alt="{{ $banner_t2->name }}">
					</a>
				@endif
    		</div>
    	</div>
    </div>


    @include('partials.brands_carusel')

@endsection
