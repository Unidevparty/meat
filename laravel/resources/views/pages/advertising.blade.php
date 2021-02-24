@extends('layouts.main')

@section('page_content')

	@php
		$banner_k  = getBanner('K');
		$banner_a3  = getBanner('A-3');
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
						<h1 class="h">{!! $page->name !!}</h1>
						@include('partials.about_menu')
					</div>
					{!! $page->text !!}
					
					<div class="mob-additional-banner">
						<a href="{{ $banner_k->fake_url }}" class="banner" target="_blank" rel="nofollow">
							<img src="{{ $banner_k->mobile_image }}" alt="{{ $banner_k->name }}">
						</a>
					</div>
					<div class="content-section banner lap-hide">
						@if (!empty($banner_a3))
							<a href="{{ $banner_a3->fake_url }}" target="_blank" rel="nofollow">
								<img src="{{ $banner_a3->mobile_image }}" alt="{{ $banner_a3->name }}">
							</a>
						@endif
					</div>


					<figure class="fig-content wbox">
						<figcaption class="h1">Аудитория портала</figcaption>
						<img src="images/_temp/graph2.jpg" alt="">
						<div class="content-row">
							<div class="content-cells">
								<div class="content-cell content-cell-6 content-cell-tab-12">
									<div class="stat-unit">
										<div class="h2">
											Ежедневная аудитория <small>(в среднем)</small>
										</div>
										<span class="nmb">>2500</span>
									</div>
								</div>
								<div class="content-cell content-cell-6 content-cell-tab-12">
									<div class="stat-unit">
										<div class="h2">
											Активных авторов <small>(хотя бы 1 публичное сообщение за месяц)</small>
										</div>
										<span class="nmb">>300</span>
									</div>
								</div>
							</div>
						</div>
					</figure>

					<div class="content-row">
						<div class="content-cells">
							<div class="content-cell content-cell-tab-12">
								<h2 class="h1">Наши предложения</h2>
								<p>Помимо стандартных медийных размещений (баннерной, текстовой рекламы), мы предлагаем воспользоваться специальными возможностями продвижения и дополнительными сервисами для повышения эффективности бизнеса:<br/>
<b>- Размещение пресс-релизов</b><br/>
Отправляйте ваши релизы на адрес: <a href="mailto:news@meat-expert.ru">news@meat-expert.ru</a><br/>
<b>- Подписка на новости и обзоры \ новостную рассылку</b><br/>
<a href="" data-pop-link="subscribe">Форма для подписки</a><br/>
<b>- Онлайн-опросы</b><br/>
<b>- Информационное партнерство</b> <br/>
Поддержка выставок и деловых мероприятий  <br/>
Рассмотрим предложения о сотрудничестве: <a href="mailto:info@meat-expert.ru">info@meat-expert.ru</a></p>
							</div>
							<div class="content-cell content-cell-mkit content-cell-tab-12">
								<h2 class="h1">Медиакит</h2>
								<div class="wbox">
									<div class="mkit-unit">
										{{--<figure>
											<img src="images/_temp/mkit.jpg" alt="">
										</figure>--}}
										<div class="mkit-unit-description">
											<div class="h"><a href="/ads/media/Miasnoi_Ekspert_2020-2021_reclamnye_vozmozhnosti_kit_razvorot.pdf">Мясной эксперт Media Kit 2020-2021</a></div>
											<div class="file-details">
												<i class="icon icon-pdf"></i>
												<b>PDF</b>, 1,6 мб.
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

					</div>
					<div class="mob-additional-banner">
						<a href="{{ $banner_p1->fake_url }}" class="banner" target="_blank" rel="nofollow">
							<img src="{{ $banner_p1->mobile_image }}" alt="{{ $banner_p1->name }}">
						</a>
					</div>
					{{-- <div class="section-header">
						<h2 class="h h1">Администрация</h2>
					</div> --}}
					{{-- <div class="staff-cards">
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
		<div class="mob-additional-banner">
			<a href="{{ $banner_k->fake_url }}" class="banner" target="_blank" rel="nofollow">
				<img src="{{ $banner_k->mobile_image }}" alt="{{ $banner_k->name }}">
			</a>
			<a href="{{ $banner_c1->fake_url }}" class="banner" target="_blank" rel="nofollow">
				<img src="{{ $banner_c1->mobile_image }}" alt="{{ $banner_c1->name }}">
			</a>
			
			<a href="{{ $banner_t1->fake_url }}" class="banner" target="_blank" rel="nofollow">
				<img src="{{ $banner_t1->mobile_image }}" alt="{{ $banner_t1->name }}">
			</a>
			<a href="{{ $banner_t2->fake_url }}" class="banner" target="_blank" rel="nofollow">
				<img src="{{ $banner_t2->mobile_image }}" alt="{{ $banner_t2->name }}">
			</a>

		</div>

	</div>

	@include('partials.brands_carusel')
@endsection