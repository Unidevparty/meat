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
					
<div class="content-row">
						<div class="content-cells">
							<div class="content-cell content-cell-6 content-cell-tab-12">
								<div class="wbox wbox-aside">
									<h3>РУКОВОДСТВО</h3>
									

			<p><strong>Владимир Романов</strong><br />
			Автор и руководитель проекта</p>

			<p><a href="mailto:expert@meat-expert.ru">expert@meat-expert.ru</a><br />
			тел.: +7 (499) 755-6785<br />
                        тел.:  +7 (499) 391-3740 (ENG)<br />
			skype: <a href="skype:meat-expert">meat-expert</a><br />
			facebook:<a href="https://www.facebook.com/MeatExpert" target="_blank">MeatExpert</a></p>
								</div>
							</div>

							<div class="content-cell content-cell-6 content-cell-tab-12">
								<div class="wbox wbox-aside">
									<h3>ОТДЕЛ РЕКЛАМЫ</h3>
									

			<p><strong>Осина Анна</strong><br /></p>
			<p><a href="mailto:a.osina@meat-expert.ru">a.osina@meat-expert.ru</a><br />
			моб.: +7 (903) 252-9682<br />
			тел.: +7 (499) 755-6785<br /></p>

								</div>
							</div>
						</div>
					</div>
					
					<div class="content-row">
						<div class="content-cells">
							<div class="content-cell content-cell-6 content-cell-tab-12">
								<div class="wbox wbox-aside">
									<h3>РЕДАКЦИЯ</h3>
										<p><strong>Виктория Загоровская</strong><br />
										Главный редактор</p>

										<p><a href="mailto:viktoria@meat-expert.ru">viktoria@meat-expert.ru</a><br />
										моб.:+7 (905) 281-7959<br />
                                                                              тел.:  +7 (499) 391-3740 (ENG)</p>

								</div>
							</div>

							<div class="content-cell content-cell-6 content-cell-tab-12">
								<div class="wbox wbox-aside">
									<h3>PR И КОММУНИКАЦИИ</h3>
										<p><strong>Глеб Котов</strong></p>
										<p><a href="mailto:gleb@meat-expert.ru">gleb@meat-expert.ru</a></p>

								</div>
							</div>
						</div>
					</div>
					
					<div class="content-row">
						<div class="content-cells">
							<div class="content-cell content-cell-6 content-cell-tab-12">
								<div class="wbox wbox-aside">
									<h3>НОВОСТНАЯ СЛУЖБА</h3>
									<p><a href="mailto:news@meat-expert.ru">news@meat-expert.ru</a> <br />
									(ждем ваши пресс-релизы)</p>

			

								</div>
							</div>

							<div class="content-cell content-cell-6 content-cell-tab-12">
								<div class="wbox wbox-aside">
									<h3>ПРАВОВАЯ ПОДДЕРЖКА</h3>
										<p><a href="mailto:pravo@meat-expert.ru">pravo@meat-expert.ru</a></p>

								</div>
							</div>
						</div>
					</div>
					
					
					<div class="content-row">
						<div class="content-cells">
							<div class="content-cell content-cell-6 content-cell-tab-12">
								<div class="wbox wbox-aside">
									<h3>ПРОЕКТИРОВАНИЕ И РЕКОНСТРУКЦИЯ</h3>
									<p><strong>Тимофей Говорухин</strong><br />
										Руководитель проектов</p>

									<p><a href="mailto:projekt@meat-expert.ru">projekt@meat-expert.ru</a></p>							
									
								</div>
							</div>

							<div class="content-cell content-cell-6 content-cell-tab-12">
								<div class="wbox wbox-aside">
									<h3>СЕРВИСНЫЙ ЦЕНТР</h3>
									<p><a href="mailto:service@meat-expert.ru">service@meat-expert.ru</a></p>
									<p>Сайт сервиса: <a href="http://service.meat-expert.ru" target="_blank">service.meat-expert.ru</a></p>
									<p>Оценка оборудования</p>
									<p><a href="mailto:oborud@meat-expert.ru">oborud@meat-expert.ru</a></p>


								</div>
							</div>
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