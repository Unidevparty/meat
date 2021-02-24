<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>{{ $banner_name }}</title>
	<style>
		html, body {
			max-width: 100%;
			max-height: 100%;
			width: 100%;
			/*height: 100%;*/
			margin: 0;
			padding: 0;
			overflow: hidden;
		}
		body * {
			margin: 0;
			padding: 0;
			display: block;
			max-width: 100%;
			max-height: 100%;
			overflow: hidden;
		}
		a {
			/*height: 100%;*/
			width: 100%;
		}

		img {
			margin: 0 auto;
		}

		.bg {
			background-position: center top;
			-webkit-background-size: 100%;
			background-size: 100%;
			background-repeat: no-repeat;
			background-color: #fff;
		}
		@if ($bg)
			html,
			body,
			a {
				height: 100% !important;
			}
		@endif
	</style>
</head>
<body>
	@if ($bg)
		<a href="{{ $banner->fake_url }}" target="_blank" style="background-image: url({{ $type == 'main' ? $banner->main_image : $banner->mobile_image }})" class="bg"></a>
	@else
		<a href="{{ $banner->fake_url }}" target="_blank">
			<img src="{{ $type == 'main' ? $banner->main_image : $banner->mobile_image }}" alt="{{ $banner_name }}">
		</a>
	@endif
</body>
</html>