@extends('layouts.auth')

@section('content')
	<div class="login-box-body">
		<p class="login-box-msg">Аторизуйтесь чтоб войти в систему</p>


		<form method="POST" action="{{ route('login') }}">
			{{ csrf_field() }}

			<div class="form-group has-feedback{{ $errors->has('email') ? ' has-error' : '' }}">
				<input id="email" type="email" class="form-control" name="email" placeholder="Email" value="{{ old('email') }}" required autofocus>
				<span class="glyphicon glyphicon-envelope form-control-feedback"></span>

				@if ($errors->has('email'))
					<span class="help-block">
						<strong>{{ $errors->first('email') }}</strong>
					</span>
				@endif
			</div>

			<div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
				<input id="password" type="password" class="form-control" name="password" required placeholder="Пароль">
				<span class="glyphicon glyphicon-lock form-control-feedback"></span>

				@if ($errors->has('password'))
					<span class="help-block">
						<strong>{{ $errors->first('password') }}</strong>
					</span>
				@endif
			</div>


			<div class="row">
				<div class="col-xs-8">
					<div class="checkbox icheck">
						<label>
							<input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Запомнить
						</label>
					</div>
				</div>
				<div class="col-xs-4">
					<button type="submit" class="btn btn-primary btn-block btn-flat">Войти</button>
				</div>
			</div>
		</form>



		<a href="{{ route('password.request') }}">Я забыл пароль</a>

	</div>
@endsection
