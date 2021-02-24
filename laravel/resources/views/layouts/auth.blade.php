<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Авторизация</title>

	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link rel="stylesheet" href="/assets/admin_assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="/assets/admin_assets/bower_components/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="/assets/admin_assets/bower_components/Ionicons/css/ionicons.min.css">
	<link rel="stylesheet" href="/assets/admin_assets/css/AdminLTE.min.css">
	<link rel="stylesheet" href="/assets/admin_assets/plugins/iCheck/square/blue.css">

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
	  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body class="hold-transition login-page">
	<div class="login-box">
		<div class="login-logo">
			<a href="/"><b>meat-expert</b>.pro</a>
		</div>

		@yield('content')
	</div>

	<script src="/assets/admin_assets/bower_components/jquery/dist/jquery.min.js"></script>
	<script src="/assets/admin_assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="/assets/admin_assets/plugins/iCheck/icheck.min.js"></script>
	<script>
		$(function() {
			$('input').iCheck({
				checkboxClass: 'icheckbox_square-blue',
				radioClass: 'iradio_square-blue',
				increaseArea: '20%'
			});
		});
	</script>
</body>

</html>
