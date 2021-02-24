@extends('layouts.main', [
	'title' => $company->title,
	'description' => $company->description,
	'keywords' => $company->keywords,
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

    @if (hasPermissions('companies'))
		<a href="{{ route('company.edit', $company->id) }}" class="edit_btn" style="margin-top: -15px;">Изменить</a>
	@endif

    <ul class="breadcrumbs">
    	<li><a href="/">Главная</a></li>
    	<li><a href="{{ route('company.list') }}">Компании</a></li>
    	<li>{{ $company->name }}</li>
    </ul>


	<div class="content-row">
		<div class="content-cells">
			<div class="content-cell content-cell-main">
				<div class="company-card-detailed wbox" style="height: auto;">
					<div class="company-card-detailed-header">
						<div class="company-card-detailed-name">
							<h1>{{ $company->name }}</h1>

							@if (!$compamy->is_holding && $company->holding_id)
								<small>Входит в состав холдинга: <a href="{{ route('company.show', $company->holding_id) }}">{{ $company->holding->name }}</a></small>
							@endif
						</div>
						@if ($company->is_checked)
							<div class="company-card-detailed-verification">
								<span class="valid-sticker">Проверено</span>
								<small>Данные верны на {{ $company->updated_at->format('d.m.Y') }}</small>
								{{-- <small>Данные верны на {{ LocalizedCarbon::instance($company->updated_at)->formatLocalized('%d %f ‘%y') }}</small> --}}
							</div>
						@endif

					</div>
					<aside>
						<figure>
							<img src="{{ resize($company->logo, 234, 234, false) }}" alt="{{ $company->name }}">
						</figure>
						<div class="compnay-card-detailed-contacts">
							<div class="company-card-detailed-stats">
								<div class="rate">{{ $company->rating }}</div>
								<span class="stats">
									<span class="stats-unit">
										<i class="icon icon-views"></i>
										{{ $company->views }}
									</span>
								</span>
							</div>
							@if ($company->site)
								<address>
									<a href="{{ $company->site }}">{{ $company->site }}</a>
								</address>
							@endif
							@if ($company->facebook || $company->google_plus || $company->vk || $company->instagram)
								<ul class="company-social-links">
									@if ($company->facebook)
										<li><a href="{{ $company->facebook }}" target="_blank"><i class="soc-icon soc-icon-fb"></i></a></li>
									@endif
									@if ($company->google_plus)
										<li><a href="{{ $company->google_plus }}" target="_blank"><i class="soc-icon soc-icon-gp"></i></a></li>
									@endif
									@if ($company->vk)
										<li><a href="{{ $company->vk }}" target="_blank"><i class="soc-icon soc-icon-vk"></i></a></li>
									@endif
									@if ($company->instagram)
										<li><a href="{{ $company->instagram }}" target="_blank"><i class="soc-icon soc-icon-ig"></i></a></li>
									@endif
								</ul>
								<hr />
							@endif

							@if ($company->email)
								<address>
									<a href="mailto:{{ $company->email }}" class="ad-iconed"><i class="ct-icon ct-icon-mail"></i>{{ $company->email }}</a>
								</address>
							@endif

							@if ($company->phone)
                                @foreach ($company->phone as $phone)
                                    <address>
                                        <a href="tel:{{ getNumber($phone->number) }}" class="ad-iconed"><i class="ct-icon ct-icon-phone"></i><b>{{ $phone->number }}</b></a>

                                        @if ($phone->description)
                                            ({{ $phone->description }})
                                        @endif
                                    </address>
                                @endforeach
								<hr />
							@endif

							@if ($company->address)
								<address>{{ $company->address }}</address>
								<address>
									<a href="http://maps.yandex.ru/?text={{ $company->coords }}" target="_blank" class="ad-iconed"><i class="ct-icon ct-icon-geo"></i>Смотреть на Яндекс.Карты</a>
								</address>
							@endif

						</div>
					</aside>
					<div class="text">
						@if ($company->types)
							<div class="description-section">
								<div class="h">Профиль компании:</div>
								<ul class="tag-list">
									@foreach ($company->types as $type)
										<li><a href="{{ route('company.list', ['profiles' => [$type->id]]) }}">{{ $type->name }}</a></li>
									@endforeach
								</ul>
							</div>
						@endif
						<div class="description-section">
							<div class="h">Описание компании:</div>
							{!! $company->text !!}
						</div>

                        @if ($company->is_holding && $company->holding_companies->count())
                            <div class="description-section">
                                <div class="h">Структура холдинга:</div>
                                <ul class="company-logos">
                                    @foreach ($company->holding_companies as $holding_company)
                                        <li>
                                            <a href="{{ route('company.show', $holding_company->alias) }}">
                                                <img src="{{ resize($holding_company->logo, 106, 106, false) }}" alt="{{ $holding_company->name }}">
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

						@if ($brands = $company->all_brands)
							<div class="description-section">
								<div class="h">Торговые марки:</div>
								<ul class="company-logos">
									@foreach ($brands as $brand)
										<li><img src="{{ resize($brand->photo, 106, 106, 0) }}" alt="{{ $brand->name }}"></li>
									@endforeach
								</ul>
							</div>
						@endif
					</div>
				</div>

				@if ($company_news->count())
					<div class="content-section">
						<h2>Новости компании</h2>
						<div class="articles-thumbs">
							@foreach ($company_news as $new)
								@include('news.thumb', ['new' => $new])
							@endforeach
						</div>
					</div>
				@endif

				@if ($company_articles->count())
					<div class="content-section">
						<h2>Статьи</h2>
						<div class="articles-thumbs">
							@foreach ($company_articles as $article)
								@include('articles.thumb', ['article' => $article])
							@endforeach
						</div>
					</div>
				@endif

				@if ($company_interviews->count())
					<div class="content-section">
						<h2>Интервью</h2>

						@foreach ($company_interviews as $interview)
							@include('interview.thumb', ['interview' => $interview])
						@endforeach
					</div>
				@endif

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

				@if ($company_jobs->count())
					<div class="content-section">
						<h2>Вакансии</h2>

						@foreach ($company_jobs as $job)
							@include('job.thumb')
						@endforeach
					</div>
				@endif

				@if ($company->gallery)
					<div class="content-section">
						<h2>Фотогалерея компании</h2>
						<div class="photos-list">

							@php
                                $gallery = $company->gallery;
                                $gallery2 = array_splice($gallery, 0, 8);
                            @endphp

							@foreach ($gallery2 as $photo)
            					<div class="photo-thumb">
            						<a href="{{ resize($photo->photo, 1920, 1080, false) }}" data-fancybox="gallery" title="{{ $photo->description }}">
            							<img src="{{ resize($photo->photo, 234, 173) }}" alt="{{ $photo->description }}">
            							<span class="h">{{ $photo->description }}</span>
            						</a>
            					</div>
                            @endforeach

                            @if ($gallery)
								@php
									foreach ($gallery as &$photo) {
										$photo->photo = resize($photo->photo, 1920, 1080, false);
										$photo->thumb = resize($photo->photo, 234, 173);
									}
								@endphp
                                <a href="#" class="btn company_load_more_gallery btn-reg btn-red wide">загрузить еще</a>
                                <script type="text/javascript">
                                    var company_gallery_more = {!! json_encode($gallery) !!}
                                </script>
                            @endif
						</div>
					</div>
				@endif


				<div class="content-section">

					@if ($company->videos)
					<h2>Видео компании</h2>

					<div class="photos-list">
						@php
							$videos = $company->videos;
							$videos2 = array_splice($videos, 0, 8);
						@endphp

						@foreach ($videos2 as $video)
							<div class="photo-thumb video-thumb">
								<a data-fancybox="" class="is-video" href="{{ $video->url }}">
									<img src="{{ resize($video->photo, 234, 173) }}" alt="{{ $video->name }}">
									<span class="h">{{ $video->name }}</span>
								</a>
							</div>
						@endforeach
						@if ($videos)
							@php
								foreach ($videos as &$video) {
									$video->thumb = resize($video->photo, 234, 173);
								}
							@endphp
							<a href="#" class="btn company_load_more_video btn-reg btn-red wide">загрузить еще</a>
							<script type="text/javascript">
								var company_videos_more = {!! json_encode($videos) !!}
							</script>
						@endif
					</div>
				@endif




				@if (!empty($banner_h1))
					<div class="mob-show mob-additional-banner nobefore">
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


				@if ($company->files)
					<div class="wbox downloads">
						<h2>Файлы</h2>

						@php
							$files = $company->files;
							$files2 = array_splice($files, 0, 5);
						@endphp

						<div class="file-list">
							@foreach ($files2 as $file)
								<div class="file-item">
									<a href="{{ $file->file }}">
										<div class="h">{{ $file->name }}</div>
										<span class="size">Файл {{ $file->ext }} - {{ $file->size }}</span>
										<span class="date">{{ $file->date }}</span>

										<i class="icon icon-file"></i>
										<i class="icon icon-dl"></i>
									</a>
								</div>
						    @endforeach

							@if ($file)
								<a href="#" class="btn company_load_more_files btn-reg btn-red wide">загрузить еще</a>
								<script type="text/javascript">
									var company_files_more = {!! json_encode($files) !!}
								</script>
							@endif
						</div>
					</div>
				@endif
			</div>



				@if (!empty($banner_k))
					<div class="content-section banner">
						<a href="{{ $banner_k->fake_url }}" class="banner" target="_blank" rel="nofollow">
							<img src="{{ $banner_k->mobile_image }}" alt="{{ $banner_k->name }}">
						</a>
					</div>
				@endif

				@if ($company->reviews()->count())
					<div class="content-section" id="reviews">
						<h2>Отзывы</h2>
						<div class="comment-thumbs">

							@foreach ($company->reviews as $review)
								<div class="blog-thumb comment-thumb">
									@if (member() && member()->is_admin())
										<a href="{{ route('company.review_delete', $review->id) }}" class="comment_delete" style="position: absolute;top: 3px;right: 20px;">Удалить</a>
									@endif
									<div class="comment-rating">
										<div class="comment-rate">
											<div class="rate">{{ $review->rate }}</div>
										</div>
										<div class="comment-thumbing">
											<a href="{{ route('company.review_like', $review->id) }}" class="negative {{ $review->likes ? 'non-zero' : '' }}">
												<i class="rate-icon rate-neg"></i>
												{{ $review->likes }}
											</a>
											<a href="{{ route('company.review_dislike', $review->id) }}" class="positive {{ $review->dislikes ? 'non-zero' : '' }}">
												<i class="rate-icon rate-pos"></i>
												{{ $review->dislikes }}
											</a>
										</div>
									</div>
									<h3>{{ $review->title }}</h3>
									<p>{!! nl2br(replace_url($review->text)) !!}</p>
									<footer>
										<div class="author-details">
											<figure><img src="{{ resize($review->member->photo, 34, 34) }}" alt="{{ $review->member->name }}"></figure>
											<a href="{{ $review->member->url() }}" class="author">{{ $review->member->name }}</a>
											<span class="date">{{ LocalizedCarbon::instance($review->created_at)->formatLocalized('%d %f ‘%y') }}</span>
										</div>
									</footer>
								</div>
							@endforeach


						</div>
					</div>
				@endif

				@if (member())
					<div class="content-section">
						<div class="error">{{ $errors->has('text') ? 'Напишите комментарий' : '' }}</div>

						{!! Form::open(['route' => 'company.review_store', 'class' => 'comment-form']) !!}
							<input type="hidden" name="company_id" value="{{ $company->id }}">
							<div class="comment-form-header">
								<h3>Оставить отзыв</h3>
								<div class="author-header author-details">
									<figure><img src="{{ resize(member()->photoUrl, 34, 34) }}" alt="{{ member()->name }}"></figure>
									<a href="{{ member()->profileUrl }}" class="author">{{ member()->name }}</a>
								</div>
							</div>
							<div class="form-row">
								<div class="rate-action">
									<label>Ваша оценка:</label>
									<div class="rate rate-ui">
										<a href=""><i></i></a>
										<a href=""><i></i></a>
										<a href=""><i></i></a>
										<a href=""><i></i></a>
										<a href=""><i></i></a>
										<input type="hidden" name="rate" value="0" class="rate-input">
									</div>
								</div>
							</div>
							<div class="form-row">
								<input type="text" name="title" placeholder="Заголовок отзыва">
							</div>
							<div class="form-row"><textarea name="text" placeholder="Ваш комментарий"></textarea></div>
							<footer class="tar">
								<input type="submit" value="отправить" class="btn btn-reg btn-red">
							</footer>
						{!! Form::close() !!}
					</div>
				@endif

			</div>
			<div class="mob-show mob-additional-banner">
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
				@if (!empty($banner_t2))
					<a href="{{ $banner_t2->fake_url }}" target="_blank" rel="nofollow">
						<img src="{{ $banner_t2->mobile_image }}" alt="{{ $banner_t2->name }}">
					</a>
				@endif
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
