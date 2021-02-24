<?php

namespace App;

class Textru
{
	static $userkey = '69b48845ed492905c2e3803cc2129df0';
	// домены разделяются пробелами либо запятыми. Данный параметр является необязательным.
	static $exceptdomain = 'meat-expert.ru';
    /*
		2 функции для взаимодействия с API Text.ru посредством POST-запросов.
		Ответы с сервера приходят в формате JSON. 
	*/

	//-----------------------------------------------------------------------
	
	/**
	 * Добавление текста на проверку
	 *
	 * @param string $text - проверяемый текст
	 * @param string $user_key - пользовательский ключ
	 * @param string $exceptdomain - исключаемые домены
	 *
	 * @return string $text_uid - uid добавленного текста 
	 * @return int $error_code - код ошибки
	 * @return string $error_desc - описание ошибки
	 */
	public static function addPost($text)
	{
		$postQuery = array();
		$postQuery['text'] = $text;
		$postQuery['userkey'] = self::$userkey;
		// домены разделяются пробелами либо запятыми. Данный параметр является необязательным.
		$postQuery['exceptdomain'] = self::$exceptdomain;
		// Раскомментируйте следующую строку, если вы хотите, чтобы результаты проверки текста были по-умолчанию доступны всем пользователям
		//$postQuery['visible'] = "vis_on";
		// Раскомментируйте следующую строку, если вы не хотите сохранять результаты проверки текста в своём архиве проверок
		//$postQuery['copying'] = "noadd";
		// Указывать параметр callback необязательно
		$postQuery['callback'] = route('textru');

		$postQuery = http_build_query($postQuery, '', '&');

		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://api.text.ru/post');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postQuery);
		$json = curl_exec($ch);
		$errno = curl_errno($ch);

		// если произошла ошибка
		$text_uid = null;
		$error_code = null;
		$error_desc = null;
		$errmsg = null;

		if ($errno) {
			$errmsg = curl_error($ch);
			dd($errmsg);
			return false;
		}

		$resAdd = json_decode($json);
		if (isset($resAdd->text_uid))
		{
			return $resAdd->text_uid;
		}
		else
		{
			$error_code = $resAdd->error_code;
			$error_desc = $resAdd->error_desc;

			dd($error_code, $error_desc);
		}

		curl_close($ch);
		return false;
	}

	/**
	 * Получение статуса и результатов проверки текста в формате json
	 *
	 * @param string $text_uid - uid проверяемого текста
	 * @param string $user_key - пользовательский ключ
	 *
	 * @return float $unique - уникальность текста (в процентах)
	 * @return string $result_json - результат проверки текста в формате json
	 * @return int $error_code - код ошибки
	 * @return string $error_desc - описание ошибки
	 */
	public static function getResultPost($uid)
	{
		$postQuery = array();
		$postQuery['uid'] = $uid;
		$postQuery['userkey'] = self::$userkey;
		// Раскомментируйте следующую строку, если вы хотите получить более детальную информацию в результатах проверки текста на уникальность
		$postQuery['jsonvisible'] = "detail";

		$postQuery = http_build_query($postQuery, '', '&');			 

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://api.text.ru/post');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postQuery);
		$json = curl_exec($ch);
		$errno = curl_errno($ch);

		if ($errno) {
			$errmsg = curl_error($ch);
			curl_close($ch);
			return false;
		}

		curl_close($ch);

		$resCheck = json_decode($json, true);
		if (!isset($resCheck['text_unique']))
		{
			$error_code = $resCheck['error_code'];
			$error_desc = $resCheck['error_desc'];

			return false;
		}

		if (!empty($resCheck['result_json'])) {
			$resCheck['result_json'] = json_decode($resCheck['result_json'], true);
		}

		if (!empty($resCheck['spell_check'])) {
			$resCheck['spell_check'] = json_decode($resCheck['spell_check'], true);
		}

		if (!empty($resCheck['seo_check'])) {
			$resCheck['seo_check'] = json_decode($resCheck['seo_check'], true);
		}

		return $resCheck;
		// $text_unique = $resCheck->text_unique;
		// $result_json = $resCheck->result_json;

		return false;
	}
}
