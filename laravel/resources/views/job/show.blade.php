@extends('layouts.main', [
	'title' => $job->title,
	'description' => $job->description,
	'keywords' => $job->keywords,
	'source_image' => $job->company->logo
])

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

	@if (hasPermissions('interview'))
		<a href="{{ route('job.edit', $job->id) }}" class="edit_btn" style="margin-top: -15px">Изменить</a>
	@endif

	<ul class="breadcrumbs">
		<li><a href="/">Главная</a></li>
		<li><a href="{{ route('job.list') }}">Вакансии</a></li>
		<li>{{ $job->name }}</li>
	</ul>


	<div class="content-row">
		<div class="content-cells">
			<div class="content-cell content-cell-main">

				<div class="company-card-detailed vacancy-card-detailed wbox">
					<figure>
						@if ($job->company->logo)
							<img src="{{ resize($job->company->logo, 86, 86, false) }}" alt="{{ $job->company->name }}">
						@endif
					</figure>

					<div class="text">
						<div class="h">
							<span class="date">Опубликовано {{ $job->published_at ? LocalizedCarbon::instance($job->published_at)->formatLocalized('%d&nbsp;%f&nbsp;‘%y') : '' }}</span>
							<span class="author">
								@if (!$job->our)
									{{-- <a href="#">{{ $job->company->name }}</a> --}}
									{{ $job->company->name }}
								@endif
								<span class="type">{{ $job->company_type->name }}</span>
							</span>
							<span class="stats">
								<span class="stats-unit">
									<i class="icon icon-views"></i>
									{{ $job->views }}
								</span>
							</span>
							<span class="company-vac-detail">
								Год основания: {{ $job->company->year_formatted }}
								<small>{{ $job->company->type }}</small>
							</span>
						</div>
						<h3>{{ $job->name }}</h3>
						<div class="description-section">
							{!! $job->introtext !!}
						</div>
						<div class="vac-stats">
						{{-- 	<span class="vac-stats-unit">
								<i class="icon icon-role"></i> Генеральный директор
							</span> --}}
							<span class="vac-stats-unit">
								<i class="icon icon-geo"></i> {{ $job->city }}
								<small>{{ $job->address }}</small>
							</span>
							<span class="vac-stats-unit">
								<i class="icon icon-income"></i>
								<b>{{ number_format($job->zarplata, 0, ',', ' ') }}<span class="rub">i</span></b>
								{!! str_replace('  ', ' <br> ', $job->zp_options) !!}
							</span>
						</div>
					</div>
				</div>


			</div>
			<div class="content-cell content-cell-aside mob-hide">
				<form action="" class="subscribe-side-form side-box">
					<div class="section-h">	Подписаться на новости</div>
					<div class="form-row"><input type="text" placeholder="Введите ваш E-mail" class="input-text"></div>
					<div class="form-row">
						<button class="btn btn-reg btn-red"><i class="icon icon-send"></i> отправить</button>
					</div>
				</form>
			</div>
		</div>
	</div>


	<div class="content-row banners-row">
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
					<div class="wbox vacancy-description">
						@if ($job->obyazannosti)
							<h4>Обязанности</h4>
							{!! $job->obyazannosti !!}
							<hr>
						@endif

						@if ($job->trebovaniya)
							<h4>Требования</h4>
							{!! $job->trebovaniya !!}
							<hr>
						@endif

						@if ($job->usloviya)
							<h4>Условия</h4>
							{!! $job->usloviya !!}
						@endif

						@if ($job->our)
							<div class="vacancy-buttons">
								@if (member())
									<a href="#" class="btn btn-reg btn-red" data-pop-link="job_call">ОТКЛИКНУТЬСЯ на вакансию</a>
								@endif
								<a href="#" class="btn btn-reg btn-orange" data-pop-link="job_call">заказать обратный звонок</a>
							</div>
						@endif

					</div>
					<div class="vacancy-person">
						{!! $job->our ? \App\Settings::getByKey('signature') : $job->signature !!}
					</div>
				</div>


				{{-- <div class="mob-show mob-additional-banner"><img src="images/_temp/viskase-hor.jpg" alt="111"></div>
				<div class="content-section banner lap-hide">
					<a href="">
						<img src="images/gea.jpg" alt="222">
					</a>
				</div> --}}


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
					<div class="mob-additional-banner">
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
				<h3>Похожие вакансии</h3>

				<?php $jobs2 = $jobs->splice(1); ?>
				@foreach ($jobs as $more_job)
					@include('job.thumb', ['job' => $more_job])
				@endforeach

				@if ($banner_c2 || $banner_p)
					<div class="mob-additional-banner">
						@if (!empty($banner_c2))
							<a href="{{ $banner_c2->fake_url }}">
								<img src="{{ $banner_c2->mobile_image }}" alt="{{ $banner_c2->name }}">
							</a>
						@endif
						@if (!empty($banner_p))
							<a href="{{ $banner_p->fake_url }}">
								<img src="{{ $banner_p->mobile_image }}" alt="{{ $banner_p->name }}">
							</a>
						@endif
					</div>
				@endif


				@foreach ($jobs2 as $more_job)
					@include('job.thumb', ['job' => $more_job])
				@endforeach


				@if ($banner_t1 || $banner_t2)
					<div class="mob-additional-banner">
						@if (!empty($banner_t1))
							<a href="{{ $banner_t1->fake_url }}">
								<img src="{{ $banner_t1->mobile_image }}" alt="{{ $banner_t1->name }}">
							</a>
						@endif
						@if (!empty($banner_t2))
							<a href="{{ $banner_t2->fake_url }}">
								<img src="{{ $banner_t2->mobile_image }}" alt="{{ $banner_t2->name }}">
							</a>
						@endif
					</div>
				@endif




				@if ($total > 3)
					<a href="{{ route('job.more_inside') }}?page=1" class="btn load_more btn-reg btn-red wide">загрузить еще</a>
				@endif
				<div class="load_more_before"></div>



			</div>

			</div>
			{{-- <div class="mob-show mob-additional-banner">
				<a href=""><img src="images/_temp/mob-banner-01.jpg" alt="999"></a>
				<a href=""><img src="images/_temp/mob-banner-02.jpg" alt="999"></a>
				<a href=""><img src="images/_temp/mob-banner-03.jpg" alt="999"></a>
				<a href=""><img src="images/_temp/mob-banner-04.jpg" alt="999"></a>
				<a href=""><img src="images/_temp/mob-banner-05.jpg" alt="999"></a>
			</div> --}}

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

@section('popups')
	<div class="pop-up job_call_popup" data-pop="job_call">
		<div class="pop-h">
			<div class="title">Заказать обратный звонок</div>
			<a href="#" class="close">
				<i class="icon icon-close"></i>
			</a>
		</div>
		<div class="pop-cnt">
			{!! Form::open(['route' => 'job_email', 'class' => 'ajax_form']) !!}
				<input type="hidden" name="page" value="{{ url(route('job.show', $job->alias)) }}">
				<input type="hidden" name="job_name" value="{{ $job->name }}">
				<input type="hidden" name="introtext" value="{{ $job->introtext }}">
				<div class="form-row">
					<label>Введите ваш e-mail</label>
					<input type="text" name="email" class="input-text wide" value="{{ member()->email }}"/>
				</div>
				<div class="form-row">
					<label>Введите ваш номер телефона</label>
					<input type="text" name="phone" class="input-text wide" value="{{ member()->phone }}"/>
				</div>
				<div class="form-row">
					<label>Введите ФИО</label>
					<input type="text" name="name" class="input-text wide" value="{{ member()->name }}"/>
				</div>
				<div class="submit-row tar">
					<div class="g-recaptcha"></div>
				</div>
				<div class="form-row">
					<input type="submit" value="Отправить" class="btn btn-reg btn-orange wide" />
				</div>
			{!! Form::close() !!}
		</div>
	</div>

	<div class="pop-up" data-pop="msg2" id="msg_popup2">
		<div class="pop-h">
			<div class="title">Спасибо за сообщение!</div>
			<a href="" class="close">
				<i class="icon icon-close"></i>
			</a>
		</div>
		<div class="pop-cnt">
			Спасибо за сообщение!
		</div>
	</div>
@endsection
