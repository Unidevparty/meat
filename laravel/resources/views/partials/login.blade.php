
<div class="pop-up" data-pop="login">
	<div class="pop-h">
		<div class="title">Вход в Личный Кабинет</div>
		<a href="" class="close">
			<i class="icon icon-close"></i>
		</a>
	</div>
	<div class="pop-cnt">
		@if ($errors && $errors->get('auth'))
			<div class="error">Вы ввели неправильный логин и/или пароль</div>
		@endif
		
		{!! Form::open(['route' => 'login']) !!}
			<div class="form-row">
				<label>Логин или e-mail</label>
				<input type="text" name="login" class="input-text wide"/>
			</div>
			<div class="form-row">
				<label>Пароль
					<a href="/forums/lostpassword/" class="remind">Забыли пароль?</a>
				</label>
				<input type="password" name="password" class="input-text wide"/>
			</div>
			<div class="form-row">
				<input type="submit" value="Войти" class="btn btn-reg btn-orange wide" />
			</div>
			<hr>
			<div class="form-row tac">
				<label>Вы еще не с нами?</label>
				<a href="/forums/register/" class="btn btn-red-bg btn-big wide"><i class="icon-reg"></i> Регистрация</a>
			</div>
		{!! Form::close() !!}
	</div>
</div>