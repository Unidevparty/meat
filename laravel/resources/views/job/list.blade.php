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
			<h1 class="h">Вакансии</h1>
		</div>
		<div class="content-cells">
			<div class="content-cell content-cell-main">
				<div class="content-section">
					<div class="filter-block">
						<form action="" method="get" class="filter">
							<div class="filter-cell">
								<select class="custom-select" name="zarplata">
									<option value="" disabled {{ !$zarplata ? 'selected' : '' }}>по зар. плате</option>
									<option value="asc" {{ $zarplata == 'asc' ? 'selected' : '' }}>По возрастанию</option>
									<option value="desc" {{ $zarplata == 'desc' ? 'selected' : '' }}>По убыванию</option>
								</select>
							</div>
							<div class="filter-cell">
								<select class="custom-select" name="city">
									<option value="" {{ !$selected_city ? 'selected' : '' }}>Все города</option>
									@foreach ($cities as $city)
										<option value="{{ $city }}" {{ $selected_city == $city ? 'selected' : '' }}>{{ $city }}</option>
									@endforeach
								</select>
							</div>
							<div class="filter-cell">
								<select class="custom-select" name="published_at">
									<option value="" disabled {{ !$published_at ? 'selected' : '' }}>по дате</option>
									<option value="desc" {{ $published_at == 'desc' ? 'selected' : '' }}>Сначала новые</option>
									<option value="asc" {{ $published_at == 'asc' ? 'selected' : '' }}>Сначала старые</option>
								</select>
							</div>
							<div class="filter-cell">
								<select class="custom-select" name="type">
									<option value="" {{ !$selected_type ? 'selected' : '' }}>Все типы</option>
									@foreach ($types as $id => $type)
										<option value="{{ $id }}" {{ $selected_type == $id ? 'selected' : '' }}>{{ $type }}</option>
									@endforeach
								</select>
							</div>
						</form>
					</div>

					<?php $jobs2 = $jobs->splice(4); ?>
					@foreach ($jobs as $job)
						@include('job.thumb', ['job' => $job])
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


	<div class="content-row banners-row show_before_on_mob">
		<div class="content-cells">
			<div class="content-cell content-cell-6 content-cell-tab-12">
				@if (!empty($banner_b1))
					<a href="{{ $banner_b1->fake_url }}" class="banner" target="_blank" rel="nofollow">
						<img src="{{ $banner_b1->main_image }}" alt="{{ $banner_b1->name }}">
					</a>
				@endif
			</div>
			<div class="content-cell content-cell-6 content-cell-tab-12">
				@if (!empty($banner_b2))
					<a href="{{ $banner_b2->fake_url }}" class="banner" target="_blank" rel="nofollow">
						<img src="{{ $banner_b2->main_image }}" alt="{{ $banner_b2->name }}">
					</a>
				@endif
			</div>
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

					<?php $jobs = $jobs2->splice(3); ?>
					@foreach ($jobs2 as $job)
						@include('job.thumb', ['job' => $job])
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
					<div class="mob-additional-banner hide_before">
						<a href="{{ $banner_h1->fake_url }}" target="_blank" rel="nofollow">
							<img src="{{ $banner_h1->mobile_image }}" alt="{{ $banner_h1->name }}">
						</a>
					</div>
				@endif

				@if (!empty($banner_c1))
					<div class="content-section banner lap-hide">
						<a href="{{ $banner_c1->fake_url }}" target="_blank" rel="nofollow">
							<img src="{{ $banner_c1->mobile_image }}" alt="{{ $banner_c1->name }}">
						</a>
					</div>
				@endif

				<div class="content-section">

					<?php $jobs2 = $jobs->splice(3); ?>
					@foreach ($jobs as $job)
						@include('job.thumb', ['job' => $job])
					@endforeach

					<div class="mob-additional-banner">
						@if (!empty($banner_c2))
							<a href="{{ $banner_c2->fake_url }}" target="_blank" rel="nofollow">
								<img src="{{ $banner_c2->mobile_image }}" alt="{{ $banner_c2->name }}">
							</a>
						@endif
						@if (!empty($banner_p))
							<a href="{{ $banner_p->fake_url }}" target="_blank" rel="nofollow">
								<img src="{{ $banner_p->mobile_image }}" alt="{{ $banner_p->name }}">
							</a>
						@endif
					</div>

					<?php $jobs = $jobs2->splice(2); ?>
					@foreach ($jobs2 as $job)
						@include('job.thumb', ['job' => $job])
					@endforeach

					@if (!empty($banner_t1) && !empty($banner_t2))
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


					@foreach ($jobs as $job)
						@include('job.thumb', ['job' => $job])
					@endforeach

					@if ($total > $first_page)
						<a href="{{ $more_url }}" class="btn load_more btn-reg btn-red wide">загрузить еще</a>
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


@section('scripts')
    <script type="text/javascript">
        $('.filter-block select')
            .not('[name="zarplata"]')
            .not('[name="published_at"]')
            .on('selectmenuchange', function() {
                var form = $(this).closest('form');

                $('select', form)
                    .not(this)
                    .not('[name="zarplata"]')
                    .not('[name="published_at"]')
                    .each(function() {
                        $(this).val('');
                    });

                form.submit();
            });
    </script>
@endsection
