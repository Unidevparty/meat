<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>{{ $page_name }} | {{ $section_name }}</title>

	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link rel="stylesheet" href="/assets/admin_assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="/assets/admin_assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
	<link rel="stylesheet" href="/assets/admin_assets/plugins/iCheck/all.css">
	<link href="/assets/admin_assets/plugins/select2/css/select2.min.css" rel="stylesheet">
	<link rel="stylesheet" href="/assets/admin_assets/bower_components/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="/assets/admin_assets/bower_components/Ionicons/css/ionicons.min.css">
	<link rel="stylesheet" href="/assets/admin_assets/css/AdminLTE.min.css">
	<link rel="stylesheet" href="/assets/admin_assets/css/skins/_all-skins.min.css">
	<link rel="stylesheet" href="/assets/admin_assets/plugins/datetimepicker/bootstrap-datetimepicker.min.css">

	<link rel="stylesheet" href="/assets/admin_assets/css/custom.css">

	<meta name="csrf-token" content="{{ csrf_token() }}">



	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  	<![endif]-->

	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body class="hold-transition skin-blue sidebar-mini">
	<div class="wrapper">

		<header class="main-header">
			<a href="/admin/" class="logo">
				<!-- mini logo for sidebar mini 50x50 pixels -->
				<span class="logo-mini"><b>A</b>LT</span>
				<!-- logo for regular state and mobile devices -->
				<span class="logo-lg"><b>Admin</b>LTE</span>
			</a>

			<nav class="navbar navbar-static-top">
				<a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
			        <span class="sr-only">Toggle navigation</span>
			        <span class="icon-bar"></span>
			        <span class="icon-bar"></span>
			        <span class="icon-bar"></span>
			    </a>

				<div class="navbar-custom-menu">
					<ul class="nav navbar-nav">
						<li><a href="/">Перейти на сайт</a></li>
						<li class="dropdown messages-menu">
							<a href="/forum/index.php?/messenger/" target="_blank">
              					<i class="fa fa-envelope-o"></i>
              					{{-- <span class="label label-success">4</span> --}}
            				</a>
						</li>
						<!-- Notifications: style can be found in dropdown.less -->
						<li class="dropdown notifications-menu">
							<a href="/forum/index.php?/notifications/">
								<i class="fa fa-bell-o"></i>
								{{-- <span class="label label-warning">10</span> --}}
							</a>
						</li>

						<!-- User Account: style can be found in dropdown.less -->
						<li class="dropdown user user-menu">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<img src="{{ resize(member()->photoUrl, 25, 25) }}" class="user-image" alt="User Image">
								<span class="hidden-xs">{{ member()->name }}</span>
							</a>
							<ul class="dropdown-menu">
								<!-- User image -->
								<li class="user-header">
									<img src="{{ resize(member()->photoUrl, 90, 90) }}" class="img-circle" alt="User Image">

									<p>
										{{ member()->name }}
										{{-- <small>Member since Nov. 2012</small> --}}
									</p>
								</li>
								<!-- Menu Body -->
								{{-- <li class="user-body">
									<div class="row">
										<div class="col-xs-4 text-center">
											<a href="#">Followers</a>
										</div>
										<div class="col-xs-4 text-center">
											<a href="#">Sales</a>
										</div>
										<div class="col-xs-4 text-center">
											<a href="#">Friends</a>
										</div>
									</div>
									<!-- /.row -->
								</li> --}}
								<!-- Menu Footer-->
								<li class="user-footer">
									<div class="pull-left">
										<a href="{{ route('admin.profile') }}" class="btn btn-default btn-flat">Профиль</a>
									</div>
									<div class="pull-right">
										<a href="{{ route('logout') }}"
										 	class="btn btn-default btn-flat"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Выход
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
									</div>
								</li>
							</ul>
						</li>
					</ul>
				</div>
			</nav>
		</header>
		<!-- Left side column. contains the logo and sidebar -->
		<aside class="main-sidebar">
			<!-- sidebar: style can be found in sidebar.less -->
			<section class="sidebar">
				<!-- Sidebar user panel -->
				<div class="user-panel">
					<div class="pull-left image">
						<img src="{{ resize(member()->photoUrl, 45, 45) }}" class="img-circle" alt="User Image">
					</div>
					<div class="pull-left info">
						<p>{{ member()->name }}</p>
						<a href="#"><i class="fa fa-circle text-success"></i> Online</a>
					</div>
				</div>
				<!-- search form -->
				<form action="#" method="get" class="sidebar-form">
					<div class="input-group">
						<input type="text" name="q" class="form-control" placeholder="Search...">
						<span class="input-group-btn">
							<button type="submit" name="search" id="search-btn" class="btn btn-flat">
								<i class="fa fa-search"></i>
							</button>
						</span>
					</div>
				</form>
				<!-- /.search form -->
				<!-- sidebar menu: : style can be found in sidebar.less -->
				<ul class="sidebar-menu" data-widget="tree">
					<li class="header">MAIN NAVIGATION</li>
					<li>
						<a href="/admin/">
							<i class="fa fa-dashboard"></i>
							<span>Главная</span>
						</a>
					</li>

					@if (hasPermissions('news'))
						<li class="{{ active(['news.*']) }}">
							<a href="{{ route('news.index') }}"><i class="fa fa-newspaper-o"></i> Новости</a>
						</li>
					@endif

					@if (hasPermissions('articles'))
						<li class="{{ active(['article.*']) }}">
							<a href="{{ route('article.index') }}"><i class="fa fa-file-text-o"></i> Статьи</a>
						</li>
					@endif

					@if (hasPermissions('interview'))
						<li class="{{ active(['interview.*']) }}">
							<a href="{{ route('interview.index') }}"><i class="fa fa-microphone"></i> Интервью</a>
						</li>
					@endif

					@if (hasPermissions('banners'))
						<li class="{{ active(['banner.*']) }}">
							<a href="{{ route('banner.index') }}"><i class="fa fa-sticky-note-o"></i> Баннеры</a>
						</li>
					@endif

					@if (hasPermissions('subscribers'))
						<li class="{{ active(['subscriber.*']) }}">
							<a href="{{ route('subscriber.index') }}"><i class="fa fa-envelope-o"></i> Подписчики</a>
						</li>
					@endif
					
					@if (hasPermissions('comments'))
						<li class="{{ active(['comments.*']) }}">
							<a href="{{ route('comments.index') }}"><i class="fa fa-envelope-o"></i> Комментарии</a>
						</li>
						
					@endif

					@if (hasPermissions('authors'))
						<li class="{{ active(['author.*']) }}">
							<a href="{{ route('author.index') }}"><i class="fa fa-edit"></i> Авторы</a>
						</li>
					@endif

					@if (hasPermissions('job'))
						<li class="treeview {{ active(['job.*', 'job_close.*']) }}">
							<a href="{{ route('job.index') }}"><i class="fa fa-briefcase"></i> Работа</a>

							<ul class="treeview-menu">
								<li class="{{ active(['job.index']) }}"><a href="{{ route('job.index') }}"><i class="fa fa-newspaper-o"></i>  Список вакансий</a></li>
								<li class="{{ active(['job_close.index']) }}"><a href="{{ route('job_close.index') }}"><i class="fa fa-circle-o"></i> Причины закрытия</a></li>
								<li class="{{ active(['job.signature']) }}"><a href="{{ route('job.signature') }}"><i class="fa fa-circle-o"></i> Подпись и пример резюме</a></li>
							</ul>
						</li>
					@endif

                    @if (hasPermissions('companies'))
						<li class="treeview {{ active(['company.*', 'company_type.*', 'companies_filter.*']) }}">
							<a href="{{ route('company.index') }}"><i class="fa fa-building-o"></i> Компании</a>

							<ul class="treeview-menu">
								<li class="{{ active(['company.index']) }}"><a href="{{ route('company.index') }}"><i class="fa fa-newspaper-o"></i>  Список компании</a></li>
								<li class="{{ active(['company_type.index']) }}"><a href="{{ route('company_type.index') }}"><i class="fa fa-circle-o"></i> Профили компаний</a></li>
								<li class="{{ active(['companies_filter.index']) }}"><a href="{{ route('companies_filter.index') }}"><i class="fa fa-circle-o"></i> Фильтры компаний</a></li>
							</ul>
						</li>
					@endif


					@if (hasPermissions('photogallery'))
						<li class="{{ active(['photogallery.*']) }}">
							<a href="{{ route('photogallery.index') }}"><i class="fa fa-image"></i> Фотогалереи</a>
						</li>
					@endif

					@if (hasPermissions('settings') || hasPermissions('meta') || hasPermissions('articles'))
						<li class="treeview {{ active(['settings.*', 'clear_cache']) }}">
							<a href="{{ route('settings.index') }}"><i class="fa fa-gear"></i> Настройки</a>
							<ul class="treeview-menu">
								@if (hasPermissions('articles'))
									<li class="{{ active(['forum_on_main.index']) }}"><a href="{{ route('forum_on_main.index') }}"><i class="fa fa-circle-o"></i> Форумы на главной</a></li>
								@endif
								@if (hasPermissions('settings'))
									<li class="{{ active(['settings.index']) }}"><a href="{{ route('settings.index') }}"><i class="fa fa-circle-o"></i> Общие</a></li>
								@endif
								@if (hasPermissions('meta'))
									<li class="{{ active(['settings.meta']) }}"><a href="{{ route('settings.meta') }}"><i class="fa fa-circle-o"></i> Метатеги</a></li>
								@endif
								@if (hasPermissions('settings'))
									<li class="{{ active(['clear_cache']) }}"><a href="{{ route('clear_cache') }}"><i class="fa fa-circle-o"></i> Удалить кеш</a></li>
									<li class="{{ active(['settings.roles']) }}"><a href="{{ route('settings.roles') }}"><i class="fa fa-circle-o"></i> Права пользователей</a></li>
								@endif
							</ul>
						</li>
					@endif

					@if (hasPermissions('templates'))
						<li class="{{ active(['pages.*']) }}">
							<a href="{{ route('pages.index') }}"><i class="fa fa-code"></i> Шаблоны</a>
						</li>
					@endif

					{{-- @if (hasPermissions('filemanager')) --}}
						<li class="{{ active(['admin.filemanager']) }}">
							<a href="{{ route('admin.filemanager') }}"><i class="fa fa-folder-open-o"></i> Файлы</a>
						</li>
					{{-- @endif --}}

					@if (hasPermissions('pages'))
						<li class="{{ active(['page.*']) }}">
							<a href="{{ route('page.index') }}"><i class="fa fa-file-o"></i> Статичные страницы</a>
						</li>
					@endif

                    @if (hasPermissions('search'))
                        <li class="treeview {{ active(['searchlog.*', 'searchwords.*']) }}">
                            <a href="#"><i class="fa fa-search"></i> Поиск</a>

                            <ul class="treeview-menu">
                                <li class="{{ active(['searchlog.*']) }}">
                                    <a href="{{ route('searchlog.index') }}"><i class="fa fa-list"></i> Лог поисковых запросов</a>
                                </li>

                                <li class="{{ active(['searchwords.*']) }}">
                                    <a href="{{ route('searchwords.index') }}"><i class="fa fa-list"></i> Поисковые сокращения</a>
                                </li>
                            </ul>
                        </li>
					@endif
				</ul>
			</section>
			<!-- /.sidebar -->
		</aside>

		<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<section class="content-header">
				<h1>{{ $section_name }} <small>{{ $page_name }}</small></h1>
				<ol class="breadcrumb">
					@foreach (breadcrumb($section_name, $page_name) as $url => $name)
						@if ($loop->first)
							<li><a href="{{ $url }}"><i class="fa fa-dashboard"></i> {{ $name }}</a></li>
						@elseif ($loop->last)
							<li class="active">{{ $name }}</li>
						@else
							<li><a href="{{ $url }}">{{ $name }}</a></li>
						@endif

					@endforeach
				</ol>
			</section>

			<!-- Main content -->
			<section class="content">

				@include('flash::message')
	            @if ($errors->any())
	                <div class="alert alert-danger">
	                    <ul>
	                        @foreach ($errors->all() as $error)
	                            <li>{{ $error }}</li>
	                        @endforeach
	                    </ul>
	                </div>
	            @endif

				@yield('content')
			</section>
			<!-- /.content -->
		</div>
		<!-- /.content-wrapper -->

		<footer class="main-footer">
			<div class="pull-right hidden-xs">
				<b>Version</b> 2.4.0
			</div>
			<strong>Copyright &copy; 2014-2016 <a href="https://adminlte.io">Almsaeed Studio</a>.</strong> All rights reserved.
		</footer>


		<!-- /.control-sidebar -->
		<!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
		<div class="control-sidebar-bg"></div>
	</div>
	<!-- ./wrapper -->

	<script src="/assets/admin_assets/bower_components/jquery/dist/jquery.min.js"></script>
	<script src="/assets/admin_assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="/assets/admin_assets/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
	<script src="/assets/admin_assets/bower_components/fastclick/lib/fastclick.js"></script>
	<script src="/assets/admin_assets/js/adminlte.min.js"></script>
	<script src="/assets/admin_assets/bower_components/ckeditor/ckeditor.js"></script>
	<script src="/assets/admin_assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
	<script src="/assets/admin_assets/plugins/iCheck/icheck.min.js"></script>
	<script src="/assets/admin_assets/plugins/select2/js/select2.full.min.js"></script>
	<script src="/assets/admin_assets/plugins/input-mask/jquery.inputmask.js"></script>
	<script src="/assets/admin_assets/plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
	<script src="/assets/admin_assets/plugins/input-mask/jquery.inputmask.extensions.js"></script>

	<script src="/assets/admin_assets/plugins/moment-with-locales.js" charset="utf-8"></script>
	<script src="/assets/admin_assets/plugins/datetimepicker/bootstrap-datetimepicker.min.js" charset="utf-8"></script>

	<script type="text/javascript">
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	</script>

	<script src="/assets/admin_assets/js/custom.js"></script>



	@yield('scripts')
</body>

</html>
