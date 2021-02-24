<div class="personal-area">
	<ul class="personal-area-tools">
		@if (member()->is_admin())
			<li>

                <a href="/forums/modcp/reports/" data-url="<?php echo  \IPS\Http\URL::internal('app=core&module=modcp&controller=modcp&tab=reports&overview=1')->csrf(); ?>" class="follow msgs-cnt warning_link">
					<i class="icon-notifications"></i>
				</a>
                <div class="personal-area-drop messages_drop">
    				<div class="title">
    					<b>Жалобы пользователей</b>
    				</div>
                    <ul class="alerts-list warnings_wrap">
                        <li class="loading">Загрузка...</li>
                    </ul>

                    <a href="/forums/modcp/reports/" class="goto_inbox">Все жалобы</a>
    			</div>
			</li>
		@endif


		<li>
			<a href="/forums/messenger/" data-url="<?php echo  \IPS\Http\URL::internal('app=core&module=messaging&controller=messenger&overview=1&_fromMenu=1')->csrf(); ?>" class="message_link follow">
				<span  class="msgs-cnt">
					<i class="icon-messages"></i>
					{{-- <span class="count">
						4
					</span> --}}
				</span>
			</a>
            <div class="personal-area-drop messages_drop">
				<div class="title">
					<b>Сообщения</b>
				</div>
                <ul class="alerts-list messages_wrap">
                    <li class="loading">Загрузка...</li>
                </ul>

                <a href="/forums/messenger/" class="goto_inbox">Перейти во входящие</a>
			</div>
		</li>
		<li class="personal-alerts">
			<a href="/forums/notifications/" data-url="<?php echo  \IPS\Http\URL::internal('app=core&module=system&controller=notifications')->csrf(); ?>" class="notifications_link follow1">
				<span  class="msgs-cnt">
					<i class="icon-alerts"></i>
					{{-- <span class="count">
						12
					</span> --}}
				</span>
			</a>
			<div class="personal-area-drop">
				<div class="title">
					<b>Уведомления</b>
					<a href="/forums/notifications/options/" class="settings">
						<i class="icon-settings"></i>
						Настройки
					</a>
				</div>
				<ul class="alerts-list notifications_wrap">
                    <li class="loading">Загрузка...</li>
				</ul>
				{{-- <ul class="alerts-list">
					<li class="unseen">
						<a href="">
							<span class="alert-img">
								<img src="images/_temp/up.jpg" alt="">
							</span>
							<span class="alert-cnt">
								<span class="h">Заявку #18 не взяли в работу</span>
								<span class="time">28 минут назад</span>
							</span>
						</a>
					</li>
				</ul> --}}
			</div>
		</li>
		<li>
			<a href="{{ member()->profileUrl }}" class="account-settings">
				<span class="account-img">
					<img src="{{ member()->photoUrl }}" alt="{{ member()->name }}">
					{{-- <span class="count">
						3
					</span> --}}
				</span>
				<span class="account-name">
					{{ str_limit(member()->name, 10) }}
				</span>
			</a>
			<div class="personal-area-drop">
				<div class="title">Контент</div>
				<ul class="personal-area-links">
					<li>
						<a href="{{ member()->profileUrl }}">Профиль{{--  <span class="count">1</span> --}}</a>
					</li>
					<li>
						<a href="/forums/attachments/">Мои вложения{{--  <span class="count">2</span> --}}</a>
					</li>
				</ul>
				<div class="title">Настройки</div>
				<ul class="personal-area-links">
					<li><a href="/forums/followed/">Управление подписками</a></li>
					<li><a href="/forums/settings/">Настройки аккаунта</a></li>
					<li><a href="/forums/ignore/">Игнорируемые пользователи</a></li>
				</ul>
				@if (member()->is_admin() || member()->is_moderator())
					<ul class="personal-area-links">
						@if (member()->is_moderator())
							<li><a href="">Панель модератора</a></li>
						@endif
						@if (member()->is_admin())
							<li>
								<a href="/admin/">
									<i class="icon-locked"></i>
									Панель администратора
								</a>
							</li>
						@endif
					</ul>
				@endif

				<ul class="personal-area-links">
					<li><a href="{{ route('logout') }}">Выйти</a></li>
				</ul>
			</div>
		</li>
	</ul>
</div>
{{-- {{ dd(member()) }} --}}
