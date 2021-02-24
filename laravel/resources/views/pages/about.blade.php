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
					<div class="fact-block" style="background-image: url(images/_temp/fact-bg.jpg)">
						<div class="in">
							<h3>Миссия</h3>
							<p>Обеспечение информационного взаимодействия специалистов мясопереработки.</p>
							<p>Увеличение роли знаний и технологий в производстве мясопродуктов.</p>
						</div>
					</div>
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

					<div class="content-row">
						<div class="content-cells">
							<div class="content-cell content-cell-6 content-cell-tab-12">
								<div class="wbox wbox-aside">
									<h3>Наши Ценности</h3>
									<ul class="checklist">
										<li>Эксперты портала</li>
										<li>Специалисты отрасли</li>
										<li>Накопленные знания и опыт</li>
										<li>Коллекция решенных проблем производства</li>
										<li>Ваше персональное развитие</li>
 										<li>Коммуникации внутри сообщества</li>      
									</ul>
								</div>
							</div>

							<div class="content-cell content-cell-6 content-cell-tab-12">
								<div class="wbox wbox-aside">
									<h3>Услуги</h3>
									<ul class="checklist">
										<li>Подбор персонала</li>
										<li>Трудоустройство специалистов</li>
										<li>Консалтинг мясоперерабатывающих предприятий</li>
										<li>Консалтинг от простой экономики до разработки стратегии</li>
										<li>Реклама на портале</li>
										<li>Независимый сервис технологического оборудования</li>
										<li>Проектирование предприятий</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<h3>Meat Expert ® is an independent professional portal for Meat Industry specialists.</h3>
					<p class="lead">Since its foundation in 2005, the portal has gathered over 9,400 experts in the field of the meat processing under its wing as a professional community. The audience of Meat Expert includes general managers of processing plants, heads of production and technologists, managers of various levels, purchasing and merchandising experts, marketing and sales specialists, as well as young scientists.<br/>
The Meat Expert community gathers leading specialists of the meat processing industry from the countries of the EAU (Russia, Belarus, Kazakhstan, Armenia, Kyrgyzstan). These are the people who shape the image of the industry and set the vector of its development. Meat Expert is the space of true knowledge and ideas.<br/>
An aspiring professional is inseparable from his close associates’ experience.
</p>
					<div class="fact-block" style="background-image: url(images/_temp/fact-bg.jpg)">
						<div class="in">
							<h3>Our Mission</h3>
							<p>Ensure information exchange platform for the specialists of the meat processing industry.</p>
							<p>Increase knowledge and enhance technology in the meat products Production.</p>
							<p>Store and multiply knowledge and practical experience.</p>
						</div>
					</div>

					<div class="mob-additional-banner">
						<a href="{{ $banner_h1->fake_url }}" class="banner" target="_blank" rel="nofollow">
							<img src="{{ $banner_h1->mobile_image }}" alt="{{ $banner_a3->name }}">
						</a>
					</div>


					<div class="content-row">
						<div class="content-cells">
							<div class="content-cell content-cell-6 content-cell-tab-12">
								<div class="wbox wbox-aside">
									<h3>Our values</h3>
									<ul class="checklist">
										<li>Our Users</li>
										<li>Stored knowledge and experience</li>
										<li>Your personal development</li>
									</ul>
								</div>
							</div>
							<div class="content-cell content-cell-6 content-cell-tab-12">
								<div class="wbox wbox-aside">
									<h3>Our services</h3>
									<ul class="checklist">
										<li>Staff recruitment for the companies</li>
										<li>Employment assistance for the  colleagues.</li>
										<li>Evaluation of equipment</li>
										<li>IT solutions for business</li>
									</ul>
								</div>
							</div>
						</div>
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