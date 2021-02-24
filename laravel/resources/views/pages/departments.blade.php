@extends('layouts.main')

@section('page_content')

	@php
		$banner_a3  = getBanner('A-3');
		$banner_k  = getBanner('K');
		$banner_b1 = getBanner('B-1');
		$banner_b2 = getBanner('B-2');
		$banner_c1 = getBanner('C-1');
		$banner_c2 = getBanner('C-2');
		$banner_p1 = getBanner('P-1');
		$banner_t1 = getBanner('T-1');
		$banner_t2 = getBanner('T-2');
	@endphp
			
	
	
	<div class="content-row">
		<div class="section-header">
			<h1 class="h">{!! $page->name !!}</h1>
			
			@include('partials.about_menu')
		</div>
		<div class="content-cells">
			<div class="content-cell content-cell-main">
				<div class="article">
					{!! $page->text !!}
				</div>
			</div>
			<div class="content-cell content-cell-aside mob-hide">
				@include('partials.subscribe_side_form')
			</div>
		</div>
	</div>


	<div class="content-row banners-row">
		<div class="content-cells mob-hide">
			<div class="content-cell content-cell-6 content-cell-tab-12">
				<a href="{{ $banner_b1->fake_url }}" class="banner" target="_blank" rel="nofollow">
					<img src="{{ $banner_b1->main_image }}" alt="{{ $banner_b1->name }}">
				</a>
			</div>
			<div class="content-cell content-cell-6 content-cell-tab-12">
				<a href="{{ $banner_b2->fake_url }}" class="banner" target="_blank" rel="nofollow">
					<img src="{{ $banner_b2->main_image }}" alt="{{ $banner_b2->name }}">
				</a>
			</div>
		</div>
		<div class="content-cells mob-show">
			<div class="content-cell content-cell-6 content-cell-tab-12">
				<a href="{{ $banner_b1->fake_url }}" class="banner" target="_blank" rel="nofollow">
					<img src="{{ $banner_b1->mobile_image }}" alt="{{ $banner_b1->name }}">
				</a>
			</div>
			<div class="content-cell content-cell-6 content-cell-tab-12">
				<a href="{{ $banner_b2->fake_url }}" class="banner" target="_blank" rel="nofollow">
					<img src="{{ $banner_b2->mobile_image }}" alt="{{ $banner_b2->name }}">
				</a>
			</div>
			<a href="{{ $banner_a3->fake_url }}" class="banner" target="_blank" rel="nofollow">
				<img src="{{ $banner_a3->mobile_image }}" alt="{{ $banner_a3->name }}">
			</a>
		</div>
	</div>



	<div class="content-row">
		<div class="content-cells">
			<div class="content-cell content-cell-main">
				<div class="content-section">
					
					{{-- <div class="section-header">
						<h2 class="h h4">Отдел продаж</h2>
					</div>
					<div class="staff-cards">
						<div class="staff-card">
							<figure><img src="images/_temp/speaker.jpg" alt=""></figure>
							<div class="staff-card-description">
								<div class="h">Иван Петров</div>
								<div class="role">Генеральный директор</div>
								<address><a href="" class="phone">+7 966 789 23 23</a></address>
								<address><a href="" class="mail">serp@meat-expert.ru</a></address>
							</div>
						</div>
						<div class="staff-card">
							<figure><img src="images/_temp/speaker.jpg" alt=""></figure>
							<div class="staff-card-description">
								<div class="h">Иван Петров</div>
								<div class="role">Генеральный директор</div>
								<address><a href="" class="phone">+7 966 789 23 23</a></address>
								<address><a href="" class="mail">serp@meat-expert.ru</a></address>
							</div>
						</div>
						<div class="mob-show mob-additional-banner"><img src="images/_temp/viskase-hor.jpg" alt=""></div>
						<div class="content-section banner lap-hide">
							<a href="">
								<img src="images/gea.jpg" alt="">
							</a>
						</div>
						<div class="staff-card">
							<figure><img src="images/_temp/speaker.jpg" alt=""></figure>
							<div class="staff-card-description">
								<div class="h">Иван Петров</div>
								<div class="role">Генеральный директор</div>
								<address><a href="" class="phone">+7 966 789 23 23</a></address>
								<address><a href="" class="mail">serp@meat-expert.ru</a></address>
							</div>
						</div>
						<div class="staff-card">
							<figure><img src="images/_temp/speaker.jpg" alt=""></figure>
							<div class="staff-card-description">
								<div class="h">Иван Петров</div>
								<div class="role">Генеральный директор</div>
								<address><a href="" class="phone">+7 966 789 23 23</a></address>
								<address><a href="" class="mail">serp@meat-expert.ru</a></address>
							</div>
						</div>
					</div>
				</div>
				<div class="content-section">
					<div class="section-header">
						<h2 class="h h4">Администрация</h2>
					</div>
					<div class="staff-cards">
						<div class="staff-card">
							<figure><img src="images/_temp/speaker.jpg" alt=""></figure>
							<div class="staff-card-description">
								<div class="h">Иван Петров</div>
								<div class="role">Генеральный директор</div>
								<address><a href="" class="phone">+7 966 789 23 23</a></address>
								<address><a href="" class="mail">serp@meat-expert.ru</a></address>
							</div>
						</div>
						<div class="staff-card">
							<figure><img src="images/_temp/speaker.jpg" alt=""></figure>
							<div class="staff-card-description">
								<div class="h">Иван Петров</div>
								<div class="role">Генеральный директор</div>
								<address><a href="" class="phone">+7 966 789 23 23</a></address>
								<address><a href="" class="mail">serp@meat-expert.ru</a></address>
							</div>
						</div>
						<div class="staff-card">
							<figure><img src="images/_temp/speaker.jpg" alt=""></figure>
							<div class="staff-card-description">
								<div class="h">Иван Петров</div>
								<div class="role">Генеральный директор</div>
								<address><a href="" class="phone">+7 966 789 23 23</a></address>
								<address><a href="" class="mail">serp@meat-expert.ru</a></address>
							</div>
						</div>
						<div class="mob-show mob-additional-banner"><img src="images/_temp/mob-banner-01.jpg" alt=""></div>
						<div class="staff-card">
							<figure><img src="images/_temp/speaker.jpg" alt=""></figure>
							<div class="staff-card-description">
								<div class="h">Иван Петров</div>
								<div class="role">Генеральный директор</div>
								<address><a href="" class="phone">+7 966 789 23 23</a></address>
								<address><a href="" class="mail">serp@meat-expert.ru</a></address>
							</div>
						</div>
						<div class="staff-card">
							<figure><img src="images/_temp/speaker.jpg" alt=""></figure>
							<div class="staff-card-description">
								<div class="h">Иван Петров</div>
								<div class="role">Генеральный директор</div>
								<address><a href="" class="phone">+7 966 789 23 23</a></address>
								<address><a href="" class="mail">serp@meat-expert.ru</a></address>
							</div>
						</div>
					</div> --}}
				</div>
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

	<div class="mob-show mob-additional-banner">
		<a href="{{ $banner_t1->fake_url }}" class="banner" target="_blank" rel="nofollow">
			<img src="{{ $banner_t1->mobile_image }}" alt="{{ $banner_t1->name }}">
		</a>
		<a href="{{ $banner_t2->fake_url }}" class="banner" target="_blank" rel="nofollow">
			<img src="{{ $banner_t2->mobile_image }}" alt="{{ $banner_t2->name }}">
		</a>
	</div>



	@include('partials.brands_carusel')
@endsection