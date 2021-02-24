@extends('layouts.main')

@section('page_content')

	@php
		$banner_k  = getBanner('K');
		$banner_h1 = getBanner('H-1');
		$banner_c1 = getBanner('C-1');
		$banner_c2 = getBanner('C-2');
		$banner_p1 = getBanner('P-1');
		$banner_t1 = getBanner('T-1');
		$banner_t2 = getBanner('T-2');
	@endphp
			
	<div class="content-row">
		<div class="content-cells">
			<div class="content-cell content-cell-main">
				<article class="article">
					<div class="section-header">
						<h1 class="h">{{ $page->name }}</h1>

						@include('partials.about_menu')
						
					</div>
					{!! $page->text !!}

					<div class="mob-additional-banner">
						<a href="{{ $banner_k->fake_url }}" class="banner" target="_blank" rel="nofollow">
							<img src="{{ $banner_k->mobile_image }}" alt="{{ $banner_k->name }}">
						</a>
					</div>
					
					<div class="content-section banner lap-hide">
						<a href="{{ $banner_a3->fake_url }}" class="banner" target="_blank" rel="nofollow">
							<img src="{{ $banner_a3->mobile_image }}" alt="{{ $banner_a3->name }}">
						</a>
					</div>

	

					<div class="mob-additional-banner">
						<a href="{{ $banner_h1->fake_url }}" class="banner" target="_blank" rel="nofollow">
							<img src="{{ $banner_h1->mobile_image }}" alt="{{ $banner_a3->name }}">
						</a>
					</div>

					<div class="mob-additional-banner">
						<a href="{{ $banner_c1->fake_url }}" class="banner" target="_blank" rel="nofollow">
							<img src="{{ $banner_c1->mobile_image }}" alt="{{ $banner_c1->name }}">
						</a>
						<a href="{{ $banner_c2->fake_url }}" class="banner" target="_blank" rel="nofollow">
							<img src="{{ $banner_c2->mobile_image }}" alt="{{ $banner_c2->name }}">
						</a>
						<a href="{{ $banner_p1->fake_url }}" class="banner" target="_blank" rel="nofollow">
							<img src="{{ $banner_p1->mobile_image }}" alt="{{ $banner_p1->name }}">
						</a>
						<a href="{{ $banner_t1->fake_url }}" class="banner" target="_blank" rel="nofollow">
							<img src="{{ $banner_t1->mobile_image }}" alt="{{ $banner_t1->name }}">
						</a>
						<a href="{{ $banner_t2->fake_url }}" class="banner" target="_blank" rel="nofollow">
							<img src="{{ $banner_t2->mobile_image }}" alt="{{ $banner_t2->name }}">
						</a>
					</div>

					
				</article>
			</div>

			<div class="content-cell content-cell-aside mob-hide">
				<a href="{{ $banner_k->fake_url }}" class="banner" target="_blank" rel="nofollow">
					<img src="{{ $banner_k->main_image }}" alt="{{ $banner_k->name }}">
				</a>
				<a href="{{ $banner_c1->fake_url }}" class="banner" target="_blank" rel="nofollow">
					<img src="{{ $banner_c1->main_image }}" alt="{{ $banner_c1->name }}">
				</a>
				<a href="{{ $banner_c2->fake_url }}" class="banner" target="_blank" rel="nofollow">
					<img src="{{ $banner_c2->main_image }}" alt="{{ $banner_c2->name }}">
				</a>
				<a href="{{ $banner_p1->fake_url }}" class="banner" target="_blank" rel="nofollow">
					<img src="{{ $banner_p1->main_image }}" alt="{{ $banner_p1->name }}">
				</a>
				<a href="{{ $banner_t1->fake_url }}" class="banner" target="_blank" rel="nofollow">
					<img src="{{ $banner_t1->main_image }}" alt="{{ $banner_t1->name }}">
				</a>
				<a href="{{ $banner_t2->fake_url }}" class="banner" target="_blank" rel="nofollow">
					<img src="{{ $banner_t2->main_image }}" alt="{{ $banner_t2->name }}">
				</a>
			</div>
		</div>
	</div>


	@include('partials.brands_carusel')
@endsection