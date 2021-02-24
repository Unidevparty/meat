@extends('layouts.main')

@section('page_content')

	@php
		$banner_h  = getBanner('H-1');
		$banner_k  = getBanner('K');
		$banner_a3 = getBanner('A-3');
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
				<div class="content-section">	
					<div class="wbox">
						<div class="notification">
							<i class="verify-icon notification-icon"></i>
							<h3>Что такое верификация?</h3>
							<p>Аудитория «Мясного Эксперта» это директора предприятий, заведующие производством и технологи, руководители различных уровней и менеджеры, закупщики и товароведы, маркетологи и коммерсанты, также молодые учёные. </p>

						</div>
					</div>
				</div>
			</div>
			<div class="content-cell content-cell-aside mob-hide">
				@include('partials.subscribe_side_form')
			</div>
		</div>
	</div>


	<div class="content-row banners-row">
		<div class="content-cells">
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
	</div>
	<div class="content-row banner-through tab-gea">
		@if (!empty($banner_a3))
			<a href="{{ $banner_a3->fake_url }}" target="_blank" rel="nofollow">
				<img src="{{ $banner_a3->tablet_image }}" alt="{{ $banner_a3->name }}">
			</a>
		@endif
	</div>



	<div class="content-row">
		<div class="content-cells">
			<div class="content-cell content-cell-main">
				<div class="article lead">
					<div class="mob-show mob-additional-banner">
						<a href="{{ $banner_k->fake_url }}" class="banner" target="_blank" rel="nofollow">
							<img src="{{ $banner_k->mobile_image }}" alt="{{ $banner_k->name }}">
						</a>
					</div>
					{!! $page->text !!}

					<h3>Выберите вариант верификации</h3>
					<div class="verification-thumbs">

						<div class="verification-thumb wbox">
							<header>
								<i class="ver-icon ver-mail"></i>
								<div class="hd">
									<div class="h">Корпоративный Email</div>
									<small>Вероятность отказа: <b>Низкая</b></small>
									<small>Срок проверки: <b>1-48 часов</b></small>
								</div>
							</header>
							<p>Ваш почтовый домен является корпоративным, и по этому адресу есть работающий сайт, относящийся к мясной промышленности и смежным отраслям.</p>
							<footer>
								{{-- <a href="">Кому это подходит?</a> --}}
								<a href="/forums/forms/3-korporativnaya-pochta/" class="btn btn-reg btn-red">пройти</a>
							</footer>	
						</div>

						<div class="verification-thumb wbox">
							<header>
								<i class="ver-icon ver-certify"></i>
								<div class="hd">
									<div class="h">Поручительство эксперта портала или пользователя портала с PRO-доступом</div>
									<small>Вероятность отказа: <b>Средняя</b></small>
									<small>Срок проверки: <b>1-14 дней</b></small>
								</div>
							</header>
							<p>Если выбранный Вами поручитель знает Вас и подтвердит, что Вы относитесь к мясопереработке, то Вы получите PRO-доступ. </p>
							<footer>
								{{-- <a href="">Кому это подходит?</a> --}}
								<a href="/forums/forms/4-poruchitelstvo/" class="btn btn-reg btn-red">пройти</a>
							</footer>	
						</div>

						<div class="mob-show mob-additional-banner">
							<a href="{{ $banner_c1->fake_url }}" class="banner" target="_blank" rel="nofollow">
								<img src="{{ $banner_c1->mobile_image }}" alt="{{ $banner_c1->name }}">
							</a>
							<a href="{{ $banner_c2->fake_url }}" class="banner" target="_blank" rel="nofollow">
								<img src="{{ $banner_c2->mobile_image }}" alt="{{ $banner_c2->name }}">
							</a>
						</div>


						<div class="verification-thumb wbox">
							<header>
								<i class="ver-icon ver-edu"></i>
								<div class="hd">
									<div class="h">Профильное образование</div>
									<small>Вероятность отказа: <b>Низкая</b></small>
									<small>Срок проверки: <b>30 минут - 72 часов</b></small>
								</div>
							</header>
							<p>Копия диплома о профильном образовании, студенческий билет или удостоверение преподавателя или научного сотрудника </p>
							<footer>
								{{-- <a href="">Кому это подходит?</a> --}}
								<a href="/forums/forms/5-rufilnoe-obrazovanie/" class="btn btn-reg btn-red">пройти</a>
							</footer>	
						</div>

						<div class="verification-thumb wbox">
							<header>
								<i class="ver-icon ver-chat"></i>
								<div class="hd">
									<div class="h">Активность на портале</div>
									<small>Вероятность отказа: <b>Средняя</b></small>
									<small>Срок проверки: <b>1 час - 1 недели</b></small>
								</div>
							</header>
							<p>Анализ причастности к профессиональному сообществу на основе оставленных сообщений и открытых тем. </p>
							<footer>
								{{-- <a href="">Кому это подходит?</a> --}}
								<a href="/forums/forms/8-aktivnost-na-portale/" class="btn btn-reg btn-red">пройти</a>
							</footer>	
						</div>

						<div class="mob-show mob-additional-banner">
							<a href="{{ $banner_t1->fake_url }}" class="banner" target="_blank" rel="nofollow">
								<img src="{{ $banner_t1->mobile_image }}" alt="{{ $banner_t1->name }}">
							</a>
							<a href="{{ $banner_t2->fake_url }}" class="banner" target="_blank" rel="nofollow">
								<img src="{{ $banner_t2->mobile_image }}" alt="{{ $banner_t2->name }}">
							</a>
						</div>


						<div class="verification-thumb wbox">
							<header>
								<i class="ver-icon ver-email"></i>
								<div class="hd">
									<div class="h">Визитная карточка</div>
									<small>Вероятность отказа: <b>Низкая</b></small>
									<small>Срок проверки: <b>1-48 часов</b></small>
								</div>
							</header>
							<p>Визитная карточка сотрудника компании, причастной к мясопереработке или снабжению МПЗ</p>
							<footer>
								{{-- <a href="">Кому это подходит?</a> --}}
								<a href="/forums/forms/6-vizitnaya-kartochka/" class="btn btn-reg btn-red">пройти</a>
							</footer>	
						</div>

						<div class="verification-thumb wbox">
							<header>
								<i class="ver-icon ver-photo"></i>
								<div class="hd">
									<div class="h">Фотодоказательство</div>
									<small>Вероятность отказа: <b>Высокая</b></small>
									<small>Срок проверки: <b>1-48 часов</b></small>
								</div>
							</header>
							<p>Если Вы пришлете скан или фото визитной карточки + скан или фото паспорта/водительского/диплома. Только так мы поймем, что визитка именно Ваша. </p>
							<footer>
								{{-- <a href="">Кому это подходит?</a> --}}
								<a href="/forums/forms/9-fotodokazatelstvo/" class="btn btn-reg btn-red">пройти</a>
							</footer>	
						</div>

						<div class="verification-thumb wbox">
							<header>
								<i class="ver-icon ver-selfy"></i>
								<div class="hd">
									<div class="h">Сэлфи / Себяшка</div>
									<small>Вероятность отказа: <b>Низкая</b></small>
									<small>Срок проверки: <b>1-72 часов</b></small>
								</div>
							</header>
							<p>Фотография себя самого с листом бумаги на котором написано "Для Мясного Эксперта" на фоне профессиональной атрибутики или на фоне колбасного завода</p>
							<footer>
								{{-- <a href="">Кому это подходит?</a> --}}
								<a href="/forums/forms/10-selfi-sebyashka/" class="btn btn-reg btn-red">пройти</a>
							</footer>	
						</div>

						<div class="verification-thumb wbox">
							<header>
								<i class="ver-icon ver-pen"></i>
								<div class="hd">
									<div class="h">Эссе</div>
									<small>Вероятность отказа: <b>Средняя</b></small>
									<small>Срок проверки: <b>30 минут - 72 часов</b></small>
								</div>
							</header>
							<p>Эссе с рассказом о себе и о том, почему мы вам должны дать PRO доступ </p>
							<footer>
								{{-- <a href="">Кому это подходит?</a> --}}
								<a href="/forums/forms/11-esse/" class="btn btn-reg btn-red">пройти</a>
							</footer>	
						</div>
					</div>
				</div>

				<div class="content-section banner">
					<a href="{{ $banner_h->fake_url }}" class="banner" target="_blank" rel="nofollow">
						<img src="{{ $banner_h->mobile_image }}" alt="{{ $banner_h->name }}">
					</a>
				</div>
				<div class="content-section banner lap-hide">
					<a href="{{ $banner_p1->fake_url }}" class="banner" target="_blank" rel="nofollow">
						<img src="{{ $banner_p1->mobile_image }}" alt="{{ $banner_p1->name }}">
					</a>
				</div>


				<div class="mob-show mob-additional-banner">
					<a href="{{ $banner_t1->fake_url }}" class="banner" target="_blank" rel="nofollow">
						<img src="{{ $banner_t1->mobile_image }}" alt="{{ $banner_t1->name }}">
					</a>
					<a href="{{ $banner_t2->fake_url }}" class="banner" target="_blank" rel="nofollow">
						<img src="{{ $banner_t2->mobile_image }}" alt="{{ $banner_t2->name }}">
					</a>
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



	@include('partials.brands_carusel')
@endsection