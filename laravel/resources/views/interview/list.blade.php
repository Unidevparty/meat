@extends('layouts.main')

@section('page_content')

	@php
		$banner_a3 = getBanner('A-3');
        $banner_b1 = getBanner('B-1');
        $banner_b2 = getBanner('B-2');
        $banner_h1 = getBanner('H-1');
        $banner_k = getBanner('K');
        $banner_c1 = getBanner('C-1');
        $banner_c2 = getBanner('C-2');
        $banner_p1 = getBanner('P-1');
        $banner_t1 = getBanner('T-1');
        $banner_t2 = getBanner('T-2');
	@endphp

	<div class="content-row">
		<div class="section-header">
			<h1 class="h">Интервью</h1>
			<ul class="tags-switch">
			<!--<li><a href="{{ route('interviews.list') }}" class="{{ active(['interviews.list'], 'current') }}">Все</a></li>
				@foreach ($tags as $tag)
				<li><a href="{{ route('interviews.tag', $tag->alias) }}" class="{{ !empty($current_tag) && $tag->alias == $current_tag->alias ? 'current' : '' }}">{{ $tag->name }}</a></li>
				@endforeach-->
			</ul>

		</div>
		<div class="content-cells">
			<div class="content-cell content-cell-main">
				<div class="content-section">
					<div class="filter-block">
						<form action="" method="get" class="filter">
							<div class="filter-cell">
								<select class="custom-select" name="author">
									<option value="">Автор не указан</option>
									@foreach ($authors as $id => $author)
										@if ($author)
											<option value="{{ $id }}" {{ $selected_author == $id ? 'selected' : '' }}>{{ $author }}</option>
										@endif
									@endforeach
								</select>
							</div>
							<div class="filter-cell">
								<select class="custom-select" name="company">
									<option value="">Компания не указана</option>
									@foreach ($companies as $company)
										<option value="{{ $company->id }}" {{ $selected_company == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
									@endforeach
								</select>
							</div>
							<div class="filter-cell">
								<select class="custom-select" name="order">
									<option value="date" {{ $order == 'date' ? 'selected' : ''}}>По дате</option>
									<option value="popular" {{ $order == 'popular' ? 'selected' : ''}}>По популярности</option>
								</select>
							</div>
						</form>
					</div>

					<?php $interviews2 = $interviews->splice(4); ?>

					@foreach ($interviews as $interview)
						@include('interview.thumb', ['interview' => $interview])
					@endforeach
				</div>
			</div>

			<div class="content-cell content-cell-aside mob-hide">
				@include('partials.subscribe_side_form')

				@php
					$banner_interview = getBanner('interview');
				@endphp
				@if (!empty($banner_interview))
					<a href="{{ $banner_interview->fake_url }}" target="_blank" rel="nofollow" class="banner">
						<img src="{{ $banner_interview->main_image }}" alt="{{ $banner_interview->name }}">
					</a>
					<br>
				@endif

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
			@if (!empty($banner_a3))
				<a href="{{ $banner_a3->fake_url }}" target="_blank" rel="nofollow">
					<img src="{{ $banner_a3->tablet_image }}" alt="{{ $banner_a3->name }}">
				</a>
			@endif
		</div>
	@endif



	<div class="content-row">
		<div class="content-cells">
			<div class="content-cell content-cell-main">
				<div class="content-section">
					<?php $interviews3 = $interviews2->splice(3); ?>
					@foreach ($interviews2 as $interview)
						@include('interview.thumb', ['interview' => $interview])
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

           {{-- @if (!empty($banner_h1))
			<div class="mob-additional-banner">
				<a href="https://us02web.zoom.us/webinar/register/WN_cBBQi3KDThWzG1AY4xwM0A" target="_blank" rel="nofollow">
					<img src="https://meat-expert.ru/ads/prizma/3.jpg" alt="аромадон">
				</a>
			</div>
			@endif
			@if (!empty($banner_h1))
			<div class="mob-hide">
				<a href="https://us02web.zoom.us/webinar/register/WN_cBBQi3KDThWzG1AY4xwM0A" target="_blank" rel="nofollow">
					<img src="https://meat-expert.ru/ads/prizma/2.jpg" alt="аромадон">
				</a>
			</div>
			@endif --}}

				<div class="content-section">
					<?php $interviews4 = $interviews3->splice(1); ?>
					@foreach ($interviews3 as $interview)
						@include('interview.thumb', ['interview' => $interview])
					@endforeach
					@if (!empty($banner_c1) || !empty($banner_c2))
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
					@endif
					<?php $interviews5 = $interviews4->splice(2); ?>
					@foreach ($interviews4 as $interview)
						@include('interview.thumb', ['interview' => $interview])
					@endforeach

					@if (!empty($banner_p1) || !empty($banner_t1))
						<div class="mob-additional-banner">
							@if (!empty($banner_p1))
								<a href="{{ $banner_p1->fake_url }}" target="_blank" rel="nofollow">
									<img src="{{ $banner_p1->mobile_image }}" alt="{{ $banner_p1->name }}">
								</a>
							@endif
							@if (!empty($banner_t1))
								<a href="{{ $banner_t1->fake_url }}" target="_blank" rel="nofollow">
									<img src="{{ $banner_t1->mobile_image }}" alt="{{ $banner_t1->name }}">
								</a>
							@endif
						</div>
					@endif

					<?php $interviews6 = $interviews5->splice(2); ?>
					@foreach ($interviews5 as $interview)
						@include('interview.thumb', ['interview' => $interview])
					@endforeach

					@if (!empty($banner_t2))
						<div class="mob-additional-banner">
							<a href="{{ $banner_t2->fake_url }}" target="_blank" rel="nofollow">
								<img src="{{ $banner_t2->mobile_image }}" alt="{{ $banner_t2->name }}">
							</a>
						</div>
					@endif

					@foreach ($interviews6 as $interview)
						@include('interview.thumb', ['interview' => $interview])
					@endforeach


					@if ($total > 27)
						<a href="{{ route('interviews.more') }}?page=1&tag_alias={{ $tag_alias }}&selected_author={{$selected_author}}&selected_company={{$selected_company}}&order={{$order}}" class="btn load_more btn-reg btn-red wide">загрузить еще</a>
					@endif
					<div class="load_more_before"></div>
				</div>
			</div>
			<div class="content-cell content-cell-aside mob-hide">
			@if (!empty($banner_k))
				<a href="{{ $banner_k->fake_url }}" class="banner" target="_blank" rel="nofollow">
					<img src="{{ $banner_k->main_image }}" alt="{{ $banner_k->name }}"></a>					
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
				.not('[name="order"]')
				.on('selectmenuchange', function() {
					var form = $(this).closest('form');

					$('select', form)
							.not(this)
							.not('[name="order"]')
							.each(function() {
								$(this).val('');
							});

					form.submit();
				});

	</script>
@endsection
