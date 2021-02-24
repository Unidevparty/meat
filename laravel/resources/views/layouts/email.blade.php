@php
	$mail_1 = getBanner('mail_1');
	$mail_2 = getBanner('mail_2');
@endphp
<table style="width:100%;" width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td align="center" style="background: #FFFFFF;font-family: tahoma, arial, sans-serif;">
			<table style="width: 644px;" width="644" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td style="padding-left: 30px;padding-right: 30px;">
						<table style="width: 644px;" width="644" cellpadding="0" cellspacing="0">
							<tr>
								<td>
									<table width="644" cellpadding="0" cellspacing="0">
										<tr>
											<td>
												<div style="line-height: 0;margin-bottom: 16px;"><a href="https://meat-expert.ru/"><img src="https://meat-expert.ru/images/email/logo.gif" alt="Логотип Мясной Эксперт" style="border:0;max-width: 100%;"></a></div>
											</td>
											<td align="right" valign="bottom">
												<div >
													<a href="https://www.facebook.com/MeatExpert"  style="border:0;margin: 0 2px;"><img src="https://meat-expert.ru/images/email/02.gif" alt="facebook Мясной Эксперт"  style="border:0;"/></a>
													<a href="https://vk.com/meat_expert" style="border:0;margin:  0 2px;"><img src="https://meat-expert.ru/images/email/03.gif" alt="Вконтакте Мясной Эксперт"  style="border:0;" /></a>													
													<a href="https://www.instagram.com/meatexpert/"  style="border:0;margin: 0 7px;"><img src="https://meat-expert.ru/images/email/01.gif" alt="Instagram Мясной Эксперт"  style="border:0;"/></a>
												</div>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td style="border-bottom: 6px solid #ffffff;border-top: 0px solid #ffffff;background: #cdcdcd;">
									<div style="line-height: 0;"><img src="https://meat-expert.ru/images/email/blank.gif" alt="blank" height="5" width="644"></div>
								</td>
							</tr>
							<tr>
								<td>
									<div style="font-size: 16px;width:644px;font-weight: bold;color: #333;font-family: tahoma, arial, sans-serif;margin-bottom: 10px;">Новости</div>
									<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse">
										<tr>
											@foreach ($news as $n)
												 <td valign="top"  valign="top">
													<table style="margin-bottom: 20px;" align="left" cellpadding="0" cellspacing="0" border="0">
														<tr>
															<td align="left">
																<div style="line-height:0;margin-bottom: 10px;">
																	<a href="{{ url(route('news.show', $n)) }}">
																		<img src="{{ resize('https://meat-expert.ru' . $n->preview, 132, 92) }}" alt="{{ $n->name }}" style="border:1px solid #cacaca;padding:0px;box-shadow: 0 0 2px rgba(0,0,0,0.1);" width="132" height="92">
																	</a>
																</div>
																<div style="width:128px;">
																	<a href="{{ url(route('news.show', $n)) }}" style="color:#000000;font-size: 14px;font-weight: normal;text-decoration: none;font-family: tahoma, arial, sans-serif;">{{ $n->name }}</a>
																</div>
															</td>
														</tr>
													</table>
												</td>
												@if ($loop->index == 3)
													</tr>
													<tr>
												@endif
											@endforeach
										</tr>
									</table>

									@if ($mail_1)
										<div style="line-height: 0;margin-bottom: 12px;margin-top: 20px;">
											<a href="{{ $mail_1->fake_url }}" target="_blank" rel="nofollow">
												<img src="{{ $mail_1->main_image }}" alt="{{ $mail_1->name }}" style="border: 0;">
											</a>
										</div>
									@endif
								</td>
							</tr>
							<tr>
								<td style="border-bottom: 12px solid #ffffff;border-top: 2px solid #ffffff;background: #cdcdcd;">
									<div style="line-height: 0;"><img src="https://meat-expert.ru/images/email/blank.gif" alt="blank" height="5" width="1"></div>
								</td>
							</tr>
							
							<tr>
								<td>
									<div style="font-size: 16px;font-weight: bold;color: #333;font-family: tahoma, arial, sans-serif;margin-bottom: 10px;">Статьи</div>

									<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse">
										<tr>

											@foreach ($articles as $a)
												<td valign="top" style="width: 31.3%; {{ $loop->index == 0 ? 'padding-right: 2%;' : $loop->index == 1 ? 'padding-right: 1%; padding-left: 1%;' : 'padding-left: 2%;'}}">
													<table style="margin-bottom: 20px;" align="left" cellpadding="0" cellspacing="0" border="0">
														<tr>
															<td align="left">
																<div style="line-height:0;margin-bottom: 10px;border:1px solid #cacaca;padding:0px;box-shadow: 0 0 2px rgba(0,0,0,0.1);">
																	<a href="{{ url(route('articles.show', $a)) }}">
																		<img src="{{ resize('https://meat-expert.ru' . $a->preview, 192, 121) }}" alt="{{ $a->name }}" style="width: 100%;" width="192" height="121">
																	</a>
																</div>
																<div>
																	<a href="{{ url(route('articles.show', $a)) }}" style="color:#000000;font-size: 14px;font-weight: normal;text-decoration: none;font-family: tahoma, arial, sans-serif;">{{ $a->name }}</a>
																</div>
															</td>
														</tr>
													</table>
												</td>
											@endforeach
										</tr>
									</table>

									{{-- <div style="line-height: 0;margin-bottom: 12px;">
										<a href="https://meat-expert.ru/">
											<img src="https://meat-expert.ru/images/email/mail-banner-h.jpg" alt="Золотая осень" style="border:0;width: 100%;height:auto;">
										</a>
									</div> --}}
								</td>
							</tr>
							
							{{--<tr>
								<td style="border-bottom: 12px solid #ffffff;border-top: 2px solid #ffffff;background: #cdcdcd;">
									<div style="line-height: 0;"><img src="https://meat-expert.ru/images/email/blank.gif" alt="blank" height="5" width="1"></div>
								</td>
							</tr>
							
							
							
							<tr>
								<td>
									<div style="font-size: 16px;font-weight: bold;color: #333;font-family: tahoma, arial, sans-serif;margin-bottom: 10px;">Интервью</div>

									<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse">
										<tr>

											@foreach ($articles as $a)
												<td valign="top" style="width: 31.3%; {{ $loop->index == 0 ? 'padding-right: 2%;' : $loop->index == 1 ? 'padding-right: 1%; padding-left: 1%;' : 'padding-left: 2%;'}}">
													<table style="margin-bottom: 20px;" align="left" cellpadding="0" cellspacing="0" border="0">
														<tr>
															<td align="left">
																<div style="line-height:0;margin-bottom: 10px;border:1px solid #cacaca;padding:0px;box-shadow: 0 0 2px rgba(0,0,0,0.1);">
																	<a href="{{ url(route('articles.show', $a->alias)) }}">
																		<img src="{{ resize('https://meat-expert.ru' . $a->preview, 192, 121) }}" alt="{{ $a->name }}" style="width: 100%;" width="192" height="121">
																	</a>
																</div>
																<div>
																	<a href="{{ url(route('articles.show', $a->alias)) }}" style="color:#000000;font-size: 14px;font-weight: normal;text-decoration: none;font-family: tahoma, arial, sans-serif;">{{ $a->name }}</a>
																</div>
															</td>
														</tr>
													</table>
												</td>
											@endforeach
										</tr>
									</table>

									
								</td>
							</tr>--}}
							
							<tr>
								<td style="border-bottom: 12px solid #ffffff;border-top: 12px solid #ffffff;background: #cdcdcd;">
									<div style="line-height: 0;"><img src="https://meat-expert.ru/images/email/blank.gif" alt="blank" height="5" width="1"></div>
								</td>
							</tr>
							
							<tr>
								<td>
									<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse">
										<tr>
											<td style="width: 50%;" valign="top">
												<table align="left" cellpadding="0" cellspacing="0" border="0">
													<tr>
														<td align="left">
															<div style="font-size: 16px;font-weight: bold;color: #333;font-family: tahoma, arial, sans-serif;margin-bottom: 10px;">Новые темы форума</div>

															<div style="max-width: 270px;">
																@foreach ($new_forums as $forum)
																	<?php
																		$forum_img = 'https://meat-expert.ru/images/email/mail-forum.jpg';
																		$exts = [
																			'.jpg',
																			'.jpeg',
																			'.png',
																			'.gif',
																		];

																		foreach ($exts as $ext) {
																			$img = 'uploads/forums/' . $forum['id'] . $ext;

																			if (is_file($img)) {
																				$forum_img = '/' . $img;
																				break;
																			}
																		}

																	?>

																	<table style="width: 100%;margin-bottom: 15px;" align="left" cellpadding="0" cellspacing="0" border="0">
																		<tr>
																			<td width="75" valign="top">
																				<div style="line-height: 0;">
																					<a href="{{ $forum['url'] }}">
																						<img src="{{ $forum_img }}" alt="{{ $forum['title'] }}" style="border: 0;" width="62" height="62">
																					</a>
																				</div>
																			</td>
																			<td valign="center">
																				<div style="font-size: 11.5px;line-height: 16.5px;color: #333333;text-decoration: none;font-weight: bold;font-family: tahoma, arial, sans-serif;">
																					<a href="{{ $forum['url'] }}" style="font-size: 11.5px;line-height: 16.5px;color: #333333;text-decoration: none;font-weight: bold;font-family: tahoma, arial, sans-serif;">
																						{{ $forum['title'] }}
																					</a>
																				</div>
																				<div style="font-size: 11.5px;line-height: 16.5px;color: #818080;text-decoration: none;font-weight: normal;font-family: tahoma, arial, sans-serif;">
																					<a href="{{ $forum['url'] }}" style="font-size: 11.5px;line-height: 16.5px;color: #818080;text-decoration: none;font-weight: normal;font-family: tahoma, arial, sans-serif;">
																						{{ $forum['forum']['name'] }}
																					</a>
																				</div>
																			</td>
																		</tr>
																	</table>
																@endforeach
															</div>
														</td>
													</tr>
												</table>
											</td>
											<td  style="width: 50%;" valign="top">
												<table align="left" cellpadding="0" cellspacing="0" border="0">
													<tr>
														<td align="left">
															<div style="font-size: 16px;font-weight: bold;color: #333;font-family: tahoma, arial, sans-serif;margin-bottom: 10px;">Популярные темы форума</div>


															@foreach ($popular_forums as $forum)
																<?php
																	$forum_img = 'https://meat-expert.ru/images/email/mail-forum.jpg';
																	$exts = [
																		'.jpg',
																		'.jpeg',
																		'.png',
																		'.gif',
																	];

																	foreach ($exts as $ext) {
																		$img = 'uploads/forums/' . $forum['id'] . $ext;

																		if (is_file($img)) {
																			$forum_img = '/' . $img;
																			break;
																		}
																	}

																?>
																<table style="width: 100%;margin-bottom: 15px;" align="left" cellpadding="0" cellspacing="0" border="0">
																	<tr>
																		<td width="75" valign="top">
																			<div style="line-height: 0;">
																				<a href="{{ $forum['url'] }}">
																					<img src="{{ $forum_img }}" alt="{{ $forum['title'] }}" style="border: 0;" width="62" height="62">
																				</a>
																			</div>
																		</td>
																		<td valign="center">
																			<div style="font-size: 11.5px;line-height: 16.5px;color: #333333;text-decoration: none;font-weight: bold;font-family: tahoma, arial, sans-serif;">
																				<a href="{{ $forum['url'] }}" style="font-size: 11.5px;line-height: 16.5px;color: #333333;text-decoration: none;font-weight: bold;font-family: tahoma, arial, sans-serif;">
																					{{ $forum['title'] }}
																				</a>
																			</div>
																			<div style="font-size: 11.5px;line-height: 16.5px;color: #818080;text-decoration: none;font-weight: normal;font-family: tahoma, arial, sans-serif;">
																				<a href="{{ $forum['url'] }}" style="font-size: 11.5px;line-height: 16.5px;color: #818080;text-decoration: none;font-weight: normal;font-family: tahoma, arial, sans-serif;">
																					{{ $forum['forum']['name'] }}
																				</a>
																			</div>
																		</td>
																	</tr>
																</table>
															@endforeach
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>

									@if ($mail_2)
										<div style="line-height: 0;margin-bottom: 12px;margin-top: 20px;">
											<a href="{{ url($mail_2->fake_url) }}" target="_blank" rel="nofollow">
												<img src="{{ $mail_2->main_image }}" alt="{{ $mail_2->name }}" style="border:0;width: 100%;height:auto;">
											</a>
										</div>
									@endif
								</td>
							</tr>
							<tr>
								<td style="border-bottom: 12px solid #ffffff;border-top: 12px solid #ffffff;background: #cdcdcd;">
									<div style="line-height: 0;"><img src="https://meat-expert.ru/images/email/blank.gif" alt="blank" height="5" width="1"></div>
								</td>
							</tr>
							
							{{-- <tr>
								<td>
									<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse">
										<tr>
											<td style="width: 50%;" valign="top">
												<table align="left" cellpadding="0" cellspacing="0" border="0">
													<tr>
														<td align="left">
															<div style="font-size: 16px;font-weight: bold;color: #333;font-family: tahoma, arial, sans-serif;margin-bottom: 10px;">Ближайшие мероприятия</div>

															<div style="max-width: 230px;">
																<div style="line-height:0;margin-bottom: 10px;border:1px solid #cacaca;padding:4px;box-shadow: 0 0 2px rgba(0,0,0,0.1);">
																	<table width="100%" cellpadding="0" cellspacing="0" border="0" height="135">
																		<tr>
																			<td valign="bottom" style="background: url(https://meat-expert.ru/images/email/mail-article-big.jpg) no-repeat center bottom;background-size: cover;">
																				<a href="https://meat-expert.ru/" style="display: block;background: #000000;background: rgba(0,0,0,0.6);color:#ffffff;font-weight: bold;line-height: 16px;padding-left: 8px;padding-right: 8px;font-size: 10px;padding-top: 6px;padding-bottom: 6px;text-decoration: none;">Международная выставка Ingredients Ingredients</a>
																			</td>
																		</tr>
																	</table>
																</div>
																<div style="line-height:0;margin-bottom: 10px;border:1px solid #cacaca;padding:4px;box-shadow: 0 0 2px rgba(0,0,0,0.1);">
																	<table width="100%" cellpadding="0" cellspacing="0" border="0" height="135">
																		<tr>
																			<td valign="bottom" style="background: url(https://meat-expert.ru/images/email/mail-article-big.jpg) no-repeat center bottom;background-size: cover;">
																				<a href="https://meat-expert.ru/" style="display: block;background: #000000;background: rgba(0,0,0,0.6);color:#ffffff;font-weight: bold;line-height: 16px;padding-left: 8px;padding-right: 8px;font-size: 10px;padding-top: 6px;padding-bottom: 6px;text-decoration: none;">Международная выставка Ingredients Ingredients</a>
																			</td>
																		</tr>
																	</table>
																</div>
																<div style="line-height:0;margin-bottom: 10px;border:1px solid #cacaca;padding:4px;box-shadow: 0 0 2px rgba(0,0,0,0.1);">
																	<table width="100%" cellpadding="0" cellspacing="0" border="0" height="135">
																		<tr>
																			<td valign="bottom" style="background: url(https://meat-expert.ru/images/email/mail-article-big.jpg) no-repeat center bottom;background-size: cover;">
																				<a href="https://meat-expert.ru/" style="display: block;background: #000000;background: rgba(0,0,0,0.6);color:#ffffff;font-weight: bold;line-height: 16px;padding-left: 8px;padding-right: 8px;font-size: 10px;padding-top: 6px;padding-bottom: 6px;text-decoration: none;">Международная выставка Ingredients Ingredients</a>
																			</td>
																		</tr>
																	</table>
																</div>
															</div>
														</td>
													</tr>
												</table>
											</td>--}}
											<td style="width: 50%;" valign="top">
												<table align="left" cellpadding="0" cellspacing="0" border="0">
													<tr>
														<td align="left">
															<div style="font-size: 16px;font-weight: bold;color: #333;font-family: tahoma, arial, sans-serif;margin-bottom: 10px;">Вакансии</div>

															@foreach ($jobs as $job)
																<table style="width: 100%;margin-bottom: 30px;" align="left" cellpadding="0" cellspacing="0" border="0">
																	<tr>
																		<td width="66" valign="top">
																			<div style="line-height: 0;">

																				@if ($job->company->logo)

																					<a href="{{ url(route('job.show', $job->alias)) }}">
																						<img src="{{ url(resize($job->company->logo, 38, 38, false)) }}" alt="{{ $job->company->name }}" width="38" style="border: 0;" >
																					</a>
																				@endif


																			</div>
																		</td>
																		<td>
																			<div style="font-size: 11.5px;line-height: 16.5px;color: #818080;text-decoration: none;font-weight: normal;font-family: tahoma, arial, sans-serif;">
																				<a href="{{ url(route('job.show', $job->alias)) }}" style="font-size: 11.5px;line-height: 16.5px;color: #818080;text-decoration: none;font-weight: normal;font-family: tahoma, arial, sans-serif;">
																					{{ $job->name }}
																				</a>
																			</div>
																		</td>
																	</tr>
																</table>
															@endforeach


														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td style="border-bottom: 12px solid #ffffff;border-top: 12px solid #ffffff;background: #cdcdcd;">
									<div style="line-height: 0;"><img src="https://meat-expert.ru/images/email/blank.gif" alt="blank" height="5" width="1"></div>
								</td>
							</tr>
							<table width="644" cellpadding="0" cellspacing="0">
										<tr>
											<td>
												<div style="line-height: 0;margin-bottom: 16px;"><a href="https://meat-expert.ru/"><img src="https://meat-expert.ru/images/email/logo.gif" alt="Логотип Мясной Эксперт" style="border:0;max-width: 100%;"></a></div>
											</td>
											<td align="right" valign="center">
												<div >
													<a href="https://www.facebook.com/MeatExpert"  style="border:0;margin: 0 2 7 2px;"><img src="https://meat-expert.ru/images/email/02.gif" alt="facebook Мясной Эксперт"  style="border:0;"/></a>
													<a href="https://vk.com/meat_expert" style="border:0;margin:  0 2 7 2px;"><img src="https://meat-expert.ru/images/email/03.gif" alt="Вконтакте Мясной Эксперт"  style="border:0;" /></a>													
													<a href="https://www.instagram.com/meatexpert/"  style="border:0;margin: 0 7 7 2px;"><img src="https://meat-expert.ru/images/email/01.gif" alt="instagram Мясной Эксперт"  style="border:0;"/></a>
												</div>
											</td>
										</tr>
									</table>							
							
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>