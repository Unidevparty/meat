<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js  index-page">
<!--<![endif]-->

<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-129597708-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-129597708-1');

    </script>

<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(703929, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/703929" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

    <script>
        document.oncopy = function() {
            var bodyElement = document.body;
            var selection = getSelection();
            var href = document.location.href;
            var copyright = "<br><br>Подробнее: <a href='" + href + "'>" + href + "</a><br>© Независимый портал для специалистов мясной индустрии «Мясной Эксперт»";
            var text = selection + copyright;
            var divElement = document.createElement('div');
            divElement.style.position = 'absolute';
            divElement.style.left = '-99999px';
            divElement.innerHTML = text;
            bodyElement.appendChild(divElement);
            selection.selectAllChildren(divElement);
            setTimeout(function() {
                bodyElement.removeChild(divElement);
            }, 0);
        };

    </script>
    <script>
        (function(s, t, e, p, f, o, r, m) {
            s[t] = s[t] || {};
            s[t][253918944] = {
                id: "ZoHL9si",
                rnd: 253918944
            };
            e.async = true;
            e.src = p + f;
            document[m](o)[r](e)
        }(window, "stepFORM_params", document.createElement("script"), document.location.protocol === "https:" ? "https:" : "http:", "//app.stepform.io/api.js?id=ZoHL9si", "head", "appendChild", "querySelector"));

    </script>
	

	<script>
		(function(s, t, e, p, f, o, r, m) {
			s[t] = s[t] || {};
			s[t][392162174] = {
				id: "x5tKddw",
				rnd: 392162174
			};
			e.async = true;
			e.src = p + f;
			document[m](o)[r](e)
		}(window,"stepFORM_params",document.createElement("script"),document.location.protocol==="https:"?"https:":"http:","//u006218.stepform.io/api.js?id=x5tKddw","head","appendChild","querySelector"));
	</script>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    @php
    $meta = getMeta();
    @endphp
    @if ($meta)
    <title>{{ $title or $meta->title }}</title>
    <meta name="description" content="{{ $description or $meta->description }}" />
    <meta name="keywords" content="{{ $keywords or $meta->keywords }}" />
    <meta property="og:title" content="{{ $title or $meta->title }}" />
    <meta property="og:description" content="{{ $description or $meta->description }}" />
    @else
    <title>{{ $title or '' }}</title>
    <meta name="description" content="{{ $description or '' }}" />
    <meta name="keywords" content="{{ $keywords or '' }}" />

    <meta property="og:title" content="{{ $title or '' }}" />
    <meta property="og:description" content="{{ $description or '' }}" />
    @endif

    @if ($source_image)
    <meta property="og:image" content="{{ $source_image ? 'https://meat-expert.ru' . $source_image : '' }}" />
    @else
    <meta property="og:image" content="https://meat-expert.ru/images/logo.png" />
    @endif

    <meta property="og:url" content="{{ url()->full() }}" />
    <meta property="og:type" content="website" />

    @if ($canonical)
    <link rel="canonical" href="{{ $canonical }}" />
    @endif

    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="/css/normalise.css">
    <link rel="stylesheet" href="/css/fancybox.css">
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/mapmodule.css">
    <link rel="stylesheet" href="/css/main2.css?v1.2">
    <!--[if lt IE 9]>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
        <![endif]-->
    @if (!env('APP_DEBUG'))
    <script src='https://www.google.com/recaptcha/api.js?onload=renderRecaptcha&render=explicit'></script>
    @endif


</head>

<body>
    <div class="page-container">
        <div class="banner-wide">
            <div class="banner-wide-content">
                @php
                $banner_a1 = getBanner('A-1');
                @endphp
                @if (!empty($banner_a1))
                <a href="{{ $banner_a1->fake_url }}" target="_blank" rel="nofollow" class="mob-hide"><img src="{{ $banner_a1->main_image }}" alt="{{ $banner_a1->name }}"></a>
                <a href="{{ $banner_a1->fake_url }}" target="_blank" rel="nofollow" class="mob-show"><img src="{{ $banner_a1->mobile_image }}" alt="{{ $banner_a1->name }}"></a>
                @endif
            </div>
        </div>

        <header class="header">
            <div class="width">
                <div class="header-row-1">
                    <a href="/" class="header-logo"><img src="/images/logo.svg" alt=""></a>
                    <span class="header-title">
                        Независимый портал<br /> для специалистов мясной индустрии
                    </span>
                    @if (empty($error_page))
                    <a href="" class="btn btn-reg btn-header-subscribe" data-pop-link="subscribe">подписаться</a>
                    @if (member())
                    @include('partials.logged')
                    @else
                    <a href="" class="btn btn-reg btn-red btn-header-personal" data-pop-link="login">
                        <span class="long">Вход / регистрация</span>
                        <span class="short">Войти</span>
                    </a>
                    @endif
                    @endif
                </div>
                <div class="header-row-2">
                    <nav class="header-nav ">
                        <a href="" class="header-nav-toggle mob-show">
                            <i class="icon icon-navi"></i>
							
                        </a>
                        <div class="additional-navi">
                            <ul>
                            </ul>
                        </div>

                        <div class="header-nav-cnt">
							
														
                            <ul class="header-nav-prime">
								<li class="mob-show"><a href="">Главная</a></li>
								{{--<li>
										<a href="/">
											<span class="long"></span>
											<span class="short">Главная</span>
										</a>
								</li>--}}
								
                                <li><a href="{{ route('news.list') }}" class="{{ active(['news.*'], 'current') }}">Новости</a></li>
                                <li><a href="{{ route('articles.list') }}" class="{{ active(['articles.*'], 'current') }}">Статьи</a></li>
                                <li><a href="{{ route('interviews.list') }}" class="{{ active(['interviews.*'], 'current') }}">Интервью</a></li>
                                <li><a href="/forums/">Форум</a></li>
                                <li><a href="{{ route('job.list') }}" class="{{ active(['job.*'], 'current') }}">Работа</a></li>
                                <li><a href="{{ route('photogallery.list') }}" class="{{ active(['photogallery.*'], 'current') }}">Галерея</a></li>
                                <li><a href="/forums/blogs/">Блоги</a></li>
                                {{-- <li class="mob-hide"><a href="/forums/">Форум</a></li> --}}

                                {{-- <li>
										<a href="{{ route('company.list') }}" class="{{ active(['company.*'], 'current') }}">
                                <span class="long">Каталог компаний</span>
                                <span class="short">Компании</span>
                                </a>
                                </li> --}}
                                {{-- <li>
										<a href="#">
											<span class="long">Календарь событий</span>
											<span class="short">События</span>
										</a>
									</li> --}}

                                <li><a href="/about" class="">О нас</a></li>
																
                                <li><a href="/uploads/media_kit/Meat_Expert_ENG.pdf" class="">EN</a></li>
                                {{-- <li class="mob-hide"><a href="">Проектирование</a></li>
									<li class="mob-hide"><a href="">Кадры</a></li>
									<li class="mob-hide"><a href="">Ингредиенты</a></li>
									<li class="mob-hide"><a href="">Бизнесу</a></li>
									<li class="mob-hide"><a href="">Объявления</a></li>
									<li class="mob-hide"><a href="">IT-Решения</a></li>
									<li class="mob-hide"><a href="https://service.meat-expert.ru/" target="_blank">Сервис</a></li> --}}

                                <li class="subscribe"><a href="" class="btn btn-reg" data-pop-link="subscribe">подписаться</a></li>
                            </ul>
                            {{-- <ul class="header-mob-services">
									<li><a href="">Проектирование</a></li>
									<li><a href="">Оценка оборудования</a></li>
									<li><a href="">PR и коммуникации</a></li>
									<li><a href="">Подбор кадров</a></li>
									<li><a href="">Правовая поддержка</a></li>
								</ul> --}}
                        </div>



                        <a href="#" class="share-call mob-show">
                            <i class="icon-share"></i>
                        </a>


                        <div class="share-box mob-show">
                            <div class="h">Поделиться записью</div>
                            <div class="social-list">
                                @include('partials.share')
                            </div>
                        </div>


                    </nav>

                    @if (!\Request::route() || \Request::route()->getName() !== 'search')
                    <a href="#" class="searh-call">
                        <i class="icon icon-search"></i>
                    </a>
                    @endif

                    <form class="form-search" action="{{ route('search') }}" method="get">
                        <div class="form-search-in">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Поиск...">
                            <button type="submit">
                                <i class="icon icon-search"></i>
                                <span>Искать</span>
                            </button>
                        </div>
                        <a href="" class="close-search">
                            <i class="icon-close"></i>
                        </a>
                    </form>

                </div>
            </div>
        </header>

        <div id="{{ $app_id or '' }}">
            @yield('top_content')

            <div class="page-content">
                @yield('page_content')

                @include('banners.bottom')
            </div>
        </div>


        @php
        $banner_a2 = getBanner('A-2');
        $banner_a3 = getBanner('A-3');
        @endphp

        @if ($banner_a2)
        <a href="{{ $banner_a2->fake_url }}" target="_blank" rel="nofollow" class="side-panel side-panel-l" style="background-image:url({{ $banner_a2->main_image }})"></a>
        @endif
        @if ($banner_a3)
        <a href="{{ $banner_a3->fake_url }}" target="_blank" rel="nofollow" class="side-panel side-panel-r" style="background-image:url({{ $banner_a3->main_image }})"></a>
        @endif


    </div>

    <div class="pop-fade"></div>
    <div class="pop-up" data-pop="subscribe">
        <div class="pop-h">
            <div class="title">Подписаться на новости</div>
            <a href="" class="close">
                <i class="icon icon-close"></i>
            </a>
        </div>
        <div class="pop-cnt">
            {!! Form::open(['route' => 'subscribe', 'class' => 'ajax_form']) !!}
            <div class="form-row">
                <label>Введите ваш e-mail</label>
                <input type="text" name="email" class="input-text wide" />
            </div>
            <div class="form-row">
                <input type="submit" value="Отправить" class="btn btn-reg btn-orange wide" />
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    <div class="pop-up" data-pop="msg" id="msg_popup">
        <div class="pop-h">
            <div class="title">Спасибо за подписку!</div>
            <a href="" class="close">
                <i class="icon icon-close"></i>
            </a>
        </div>
        <div class="pop-cnt">
            Спасибо за подписку!
        </div>
    </div>

    @yield('popups')

    @include('partials.login')

    <footer class="footer">
        <div class="width">
            <div class="footer-row-1">
                <div class="footer-navi">
                    <ul>
                        <li>
                            <ul>
                                <li><a href="{{ route('news.list') }}" class="{{ active(['news.*'], 'current') }}">Новости</a></li>
                                <li><a href="{{ route('articles.list') }}" class="{{ active(['articles.*'], 'current') }}">Статьи</a></li>
                                <li><a href="{{ route('interviews.list') }}" class="{{ active(['interviews.*'], 'current') }}">Интервью</a></li>
                                <li><a href="{{ route('job.list') }}" class="{{ active(['job.*'], 'current') }}">Работа</a></li>
                                <li><a href="/forums/blogs/">Блоги</a></li>
                                <li><a href="/forums/">Форум</a></li>
                                <li><a href="/about">О нас</a></li>
                            </ul>

                        </li>
                        <li>
                            <ul>
                                {{-- <li><a href="#">Каталог компаний</a></li>
									<li><a href="#">Календарь событий</a></li>--}}
                                <li><a href="https://service.meat-expert.ru/" target="_blank">Сервис</a></li>
                                <li><a href="{{ route('photogallery.list') }}">Галерея</a></li>
                                <li><a href="/uploads/media_kit/Meat_Expert_ENG.pdf" class="">EN</a></li>

                            </ul>
                        </li>
                        <li>
                            <ul>
                                <li class="social-links">
                                    <a href="https://www.facebook.com/MeatExpert" target="_blank" title="Фейсбук автора" rel="nofollow">
                                        <i class="icon icon-soc-fb"></i>
                                    </a>
                                    <a href="https://www.facebook.com/TheMeatExpert2/" target="_blank" title="Сообщество" rel="nofollow">
                                        <i class="icon icon-soc-fb">2</i>
                                    </a>
                                    <a href="https://vk.com/meat_expert" target="_blank" title="Сообщество ВК" rel="nofollow">
                                        <i class="icon icon-soc-vk"></i>
                                    </a>
                                </li>
                            </ul>


                        </li>
                    </ul>
                    {!! Form::open(['route' => 'subscribe', 'class' => 'subscription-form ajax_form']) !!}
                    <div class="caption">Подписаться на новости</div>
                    <div class="input-place input-text">
                        <input type="text" name="email" placeholder="Введите ваш E-mail">
                        <button type="submit">
                            <i class="icon icon-send"></i>
                        </button>
                    </div>
                    {!! Form::close() !!}
                </div>
                <div class="footer-feedback">
                    {!! Form::open(['route' => 'feedback', 'class' => 'ajax_form']) !!}
                    <input type="hidden" name="form" value="footer_form">
                    <div class="h">Обратная связь</div>
                    <fieldset>
                        <div class="form-row"><input type="text" class="input-text" name="name" placeholder="Имя"></div>
                        <div class="form-row"><input type="text" class="input-text" name="phone" placeholder="Контактный телефон"></div>
                        <div class="form-row"><input type="email" class="input-text" name="email" placeholder="E-mail"></div>
                    </fieldset>
                    <fieldset>
                        <textarea placeholder="Сообщение" class="textarea" name="msg"></textarea>
                    </fieldset>
                    <div class="copyright">Пресс-релизы слать на news{собака}meat-expert.ru<br><br></div>
                    <div class="submit-row tar">
                        <div class="g-recaptcha"></div>
                        <input type="submit" value="Отправить" class="btn btn-reg btn-bardo">
                    </div>
                    {!! Form::close() !!}

                </div>
                <a href="" class="scroll-top">
                    <span>наверх</span>
                    <span class="icn">
                        <i class="icon icon-arr-top"></i>
                    </span>
                </a>
            </div>
            <div class="footer-row-2 clearfix">
                <div class="copyright">© 2005-{{ date('Y') }} Независимый портал «Мясной Эксперт»</div>


            </div>
            <div class="footer-row-2 clearfix">
                <div class="copyright">Salus populi suprema lex – «Здоровье народа – высший закон»</div>

                <ul class="footer-additional-links">
                    <li><a href="/conditions">Условия пользования сайтом</a></li>
                    <li><a href="/Privacy-policy">Политика конфиденциальности</a></li>
                    <li><a href="/Agreement-on-the-use">Соглашение о пользовании сайтом</a></li>
                </ul>
                <div class="yacounter">
                    <a href="https://webmaster.yandex.ru/sqi?host=meat-expert.ru"><img width="88" height="31" alt="" border="0" src="https://yandex.ru/cycounter?meat-expert.ru&theme=dark&lang=ru" /></a>
                </div>
            </div>
        </div>
    </footer>






    <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" crossorigin="anonymous"></script>
    <script type="text/javascript">
        if (typeof jQuery == 'undefined') {
            document.write(unescape("%3Cscript src='/js/jquery-3.2.1.min.js' type='text/javascript'%3E%3C/script%3E"));
        }

        function YourOnSubmitFn() {
            console.log('run');
        }

    </script>
    <script src="/js/jquery-migrate-1.4.1.min.js"></script>
    <script src="/js/jquery-ui.min.js"></script>
    <script src="/js/jquery.bxslider.min.js"></script>
    <script src="/js/fancybox.js"></script>
    <script src="/js/main.js?v2"></script>
    <script src="/js/added.js"></script>
    <script src="/js/main2.js"></script>
    <script src="/js/gascrolldepth.js"></script>
    <script src="/js/scrolld.js"></script>
    @if ($errors && $errors->get('auth'))
    <script>
        var pop = $('.pop-up[data-pop="login"]');
        $('html').addClass('pop-called');
        $('.pop-fade').show().css('height', $(document).height());
        pop.addClass('pop-shown').css({
            'top': $(window).scrollTop() + $(window).height() / 2 - pop.height() / 2,
            "marginLeft": pop.width() / -2
        });

    </script>


    @endif
    @php
    $scripts = \App\Settings::getByKey('bottom_scripts');
    @endphp
    @if (is_string($scripts))
    {!! $scripts !!}
    @endif

    @yield('scripts')

    <script>
        var recaptcha_widgets = [];

        function renderRecaptcha() {
            $('.g-recaptcha').each(function() {
                recaptcha_widgets[recaptcha_widgets.length] = grecaptcha.render(this, {
                    'sitekey': '6Lch4ggUAAAAADDnp66HjJdXqyqqmTknfE_icr4k',
                    'theme': 'light'
                });
            });
        }

    </script>
</body>

</html>
