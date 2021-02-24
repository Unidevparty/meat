@extends('layouts.auth')

@section('content')
	<div class="register-box-body">
		<p class="login-box-msg">Регистрация</p>

		<form method="POST" action="{{ route('register') }}">
			{{ csrf_field() }}

			<div class="form-group has-feedback{{ $errors->has('name') ? ' has-error' : '' }}">
				<input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="Имя" required autofocus>
				<span class="glyphicon glyphicon-user form-control-feedback"></span>

				@if ($errors->has('name'))
					<span class="help-block">
						<strong>{{ $errors->first('name') }}</strong>
					</span>
				@endif
			</div>


			<div class="form-group has-feedback{{ $errors->has('email') ? ' has-error' : '' }}">
				<input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email" required>

				@if ($errors->has('email'))
					<span class="help-block">
						<strong>{{ $errors->first('email') }}</strong>
					</span>
				@endif
			</div>


			<div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
				<input id="password" type="password" class="form-control" name="password" placeholder="Пароль" required>
				<span class="glyphicon glyphicon-lock form-control-feedback"></span>

				@if ($errors->has('password'))
					<span class="help-block">
						<strong>{{ $errors->first('password') }}</strong>
					</span>
				@endif
			</div>

			<div class="form-group has-feedback">
				<input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
				<span class="glyphicon glyphicon-log-in form-control-feedback"></span>
			</div>


			<div class="row">
				<div class="col-xs-12">
					<div class="checkbox icheck">
						<label>
	              <input type="checkbox"> Я прочитал и согласен с <a href="#">условиями</a>
	            </label>
					</div>
				</div>

				<div class="col-xs-12">
					<button type="submit" class="btn btn-primary btn-block btn-flat">Зарегистрироваться</button>
				</div>
			</div>

			<a href="{{ route('login') }}" class="text-center">Я уже зарегистрирован</a>
		</form>
	</div>
@endsection
