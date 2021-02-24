<?php

function checkPermissions($perm_name) {
	if (!member() || !member()->hasRule($perm_name)) {
        abort('404');
    }
}
function hasPermissions($perm_name) {
	if (member() && member()->hasRule($perm_name)) {
        return true;
    }

    return false;
}

function check_recaptcha($response, $private_key) {

	$url = 'https://www.google.com/recaptcha/api/siteverify';

	$data = array(
		'secret' => $private_key,
		'response' => $response
	);

	// $options = array(
	// 	'http' => array (
	// 		'method' => 'POST',
	// 		'content' => http_build_query( $data)
	// 	)
	// );
	// $context = stream_context_create($options);
	// $verify = file_get_contents($url, false, $context);
	// $captcha_success = json_decode($verify);



	if( $curl = curl_init() ) {
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
		$out = curl_exec($curl);
		curl_close($curl);

		$captcha_success = json_decode($out);

		return $captcha_success->success;
	}

	return false;
}

function breadcrumb($section_name = '', $page_name = '')
{
	$breadcrumb = ['/admin' => 'Главная'];

	$route_name = \Route::currentRouteName();
	$route_uri  = \Route::getFacadeRoot()->current()->uri();
	$route_path = explode('.', $route_name);

	if (!$route_name) return $breadcrumb;

	if (Route::has($route_path[0] . '.index')) {
		$breadcrumb[route($route_path[0] . '.index')] = $section_name;
	}

	if (end($route_path) == 'index') {
		return $breadcrumb;
	}

	$breadcrumb[$route_uri] = $page_name;

	return $breadcrumb;
}


// Сохраняет картинку после кропа
function saveCroppedImage($image, $image_name) {

	if (!preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
		return;
	}

	$image = substr($image, strpos($image, ',') + 1);
	$type = strtolower($type[1]); // jpg, png, gif

	if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
		throw new \Exception('invalid image type');
	}

	$image = base64_decode($image);

	if ($image === false) {
		return;
	}

	// Папка с картинками
	$img_path = config('app.content_images_path');

	$date = date('Y/m/');
	$folder = app_path() . "/../.." . $img_path . $date;

	if (!is_dir($folder)) {
		mkdir($folder, 0777, 1);
	}

	file_put_contents($folder . $image_name . '.' . $type, $image);

	return $img_path . $date . $image_name . '.' . $type;
}

function saveUploadedImage($image, $name, $input_name = 'source_image') {
	if (!$image) return;

	// Папка с картинками
	$img_path = config('app.content_images_path');

	$date = date('Y/m/');
	$folder = app_path() . "/../.." . $img_path . $date;

	if (!is_dir($folder)) {
		mkdir($folder, 0777, 1);
	}

	$image_name = build_file_name($name) . '.' . $image->getClientOriginalExtension();

    $image->move($folder, $image_name);

    return $img_path . $date . $image_name;
}

function build_file_name($name) {
	return substr(str_slug($name), 0, 8) . '_' . microtime(1);
}

function ipb_require() {
	require_once app_path('../../forums/init.php');

	\IPS\Dispatcher\Build::i();
}
function member() {
	return \App\Member::get();
}


function save_image($image, $model_image, $image_name) {
	if (!$image) return;

	$old_image = app_path() . '/../..' . $model_image;
	if ($model_image && is_file($old_image)) {
		unlink($old_image);
	}

	return saveCroppedImage($image, $image_name);
}

/**
 * Возвращает баннер
 * @param  string $position [A-1, B-1, B-2, C-1, C-2, H-1, K, P-1, T-1, T-2, X-1, X-2]
 * @param  string $type     [main_image, mobile_image]
 * @return [string]         [image url]
 */
// function getBanner($position) {
// 	$session = session('banner', []);

// 	$banners = \App\Banner::where('position', $position)->where('bydefault', '=', 0)->published()->get();

// 	$need_banner = $banners[0];

// 	$in_session = false;
// 	if (count($banners) > 1) {
// 		foreach ($banners as $banner) {
// 			if (!in_array($banner->id, $session[$position])) {
// 				$need_banner = $banner;
// 				$in_session = true;
// 				break;
// 			}
// 		}
// 	}

// 	if (!$need_banner) {
// 		$need_banner = \App\Banner::where('position', $position)->where('bydefault', '=', 1)->first();
// 		if (!$need_banner) {
// 			return null;
// 		}
// 	}

// 	$need_banner->views = $need_banner->views + 1;
// 	$need_banner->save();

// 	if (!$in_session) {
// 		$session[$position] = [];
// 	}
// 	$session[$position][] = $need_banner->id;
// 	Illuminate\Support\Facades\Session::put('banner', $session);
// 	Illuminate\Support\Facades\Session::save();

// 	return $need_banner;
// }
//
function getBanner($position) {

	$session = !empty($_SESSION['banner']) ? $_SESSION['banner'] : [];
	$banner_showed = !empty($_SESSION['banner_showed']) ? $_SESSION['banner_showed'] : [];

    $banners = \App\Banner::where('position', $position)->where('bydefault', '=', 0)->published()->get();

	$need_banner = $banners[0];

	$in_session = false;
	if (count($banners) > 1) {
		foreach ($banners as $banner) {
			if (!in_array($banner->id, $session[$position])) {
				$need_banner = $banner;
				$in_session = true;
				break;
			}
		}
	}

	if (!$need_banner) {
		$need_banner = \App\Banner::where('position', $position)->where('bydefault', '=', 1)->first();
		if (!$need_banner) {
			return null;
		}
    }

    if (empty($banner_showed[$need_banner->id])) {
        $banner_showed[$need_banner->id] = 1;
    	$need_banner->views++;
    	$need_banner->save();
    } else {
        $banner_showed[$need_banner->id]++;
    }

	if (!$in_session) {
		$session[$position] = [];
	}
	$session[$position][] = $need_banner->id;

	$_SESSION['banner_showed'] = $banner_showed;
	$_SESSION['banner'] = $session;

	return $need_banner;
}


function resize($image, $width, $height, $crop = true, $noimage = '/images/noimage.svg')
{
    if (!$image) return $noimage;

    $app_url = isset($_SERVER['APP_URL']) ? $_SERVER['APP_URL'] : '';
    $image = str_replace($app_url, '', $image);

	if (strpos($image, 'http') === 0) {
		return $image;
	}

    $image = trim($image, '/');

    // if (is_file($image) && filesize($image) > 2048 * 1024) {
    //     return $noimage;
    // }

    $ext = last(explode('.', $image));
    $img_name = last(explode('/', $image));
    $new_img_name = substr($img_name, 0, 8) . '-' . $width . 'x' . $height . '-' . md5($img_name);
    $new_img_name = str_slug($new_img_name) . '.' . $ext;

    $new_file = 'images_cache/' . $new_img_name;

    try {
        if (file_exists($image) && !file_exists($new_file)) {
            $img = \Intervention\Image\Facades\Image::make($image);
    		if ($crop) {
            	$img->fit($width, $height);
    		} else {
    			$img->resize($width, $height,function ($constraint) {
    				$constraint->aspectRatio();
    			});
    		}
            $img->save($new_file);
        }

        return '/' . $new_file;
    } catch (\Intervention\Image\Exception\NotReadableException $e) {
       return $noimage;
    }
}


function getPartnersBanners() {
	return \App\Banner::where('position', 'partner')->published()->get();
}

/**
 * Обрезает текст
 *
 * @param string $string Текст
 * @param int $length Длина для обрезки
 * @param string $etc Что добавить в конце обрезки
 * @param bool $break_words Прерывать сова или нет
 * @param string $charset кодировка
 * @return string
 */
function cut_text($string, $length = 80, $etc = '...', $break_words = false, $charset = 'UTF-8') {
    if ($length == 0) {
        return '';
    }
    if (strlen($string) > $length) {
        $length -= min($length, strlen($etc));
        if (!$break_words) {
            $string = preg_replace('/\s+?(\S+)?$/', '', mb_substr($string, 0, $length + 1, $charset));
        }
        return mb_substr($string, 0, $length, $charset) . $etc;
    } else {
        return $string;
    }
}

/**
 * Обрезает текст на 2 части по тегам, первая часть составляет указанные проценты
 * @param  string  $text HTML
 * @param  integer $tags к-во строк первой части
 * @param  integer $percentage процент первой части
 * @return array массив 2х кусков текста
 */
function slit_text($text, $tags = 0, $percentage = 30) {
	$text = str_replace('<p>&nbsp;</p>', '', $text);
	$text = str_replace('sup>', 'surrr>', $text);

	preg_match_all('#<([a-z\d]+)[^/>]*(?:/>|>(?:.+\1>))#Uis',$text,$res);

	$content = $res[0];

	$content = array_map(function ($e) {
		return str_replace('surrr>', 'sup>', $e);
	}, $content);

	$tags_count = count($content);

	if (!$tags) {
		$tags = ceil($tags_count * $percentage / 100);
	}

	if($tags >= $tags_count) {
		return [
			implode("\n", $content),
			''
		];
	} elseif ($tags == 0) {
		return [
			'',
			implode("\n", $content)
		];
	}

	$result = [];

	for ($i = 0; $i < $tags_count; $i++) {
		if ($i < $tags) {
			$result[0] .= $content[$i];
		} else {
			$result[1] .= $content[$i];
		}
	}

	return $result;
}


function getMeta($current = '') {
	if (!$current) {
		$current = url()->current();
		$current = str_replace(trim(config('app.url'), '/'), '', $current);
	}

	if (!$current) $current = '/';

	$meta = App\Meta::where('url', $current)->first();

	return $meta;
}



if(!function_exists('str_putcsv'))
{
    function str_putcsv($input, $delimiter = ',', $enclosure = '"')
    {
        // Open a memory "file" for read/write...
        $fp = fopen('php://temp', 'r+');
        // ... write the $input array to the "file" using fputcsv()...
        fputcsv($fp, $input, $delimiter, $enclosure);
        // ... rewind the "file" so we can read what we just wrote...
        rewind($fp);
        // ... read the entire line into a variable...
        $data = fread($fp, 1048576);
        // ... close the "file"...
        fclose($fp);
        // ... and return the $data to the caller, with the trailing newline from fgets() removed.
        return rtrim($data, "\n");
    }
}

function replace_url($text) {
	return preg_replace('#(http|https)://([^\s]+)#i', '<a href="$1://$2" target="_blank">$1://$2</a>', $text);
}


/**
 * Возвращает список компаний (для админки)
 **/
function get_companies_list() {
	$companies = App\Company::pluck('name', 'id')->toArray();

	$companies[0] = 'Не указано';

	ksort($companies);

	return $companies;
}

/**
 * Возвращает список компаний (для админки)
 **/
function get_events_list() {
	//$events = App\Event::pluck('name', 'id')->toArray();

	$events[0] = 'Не указано';

	ksort($events);

	return $events;
}

/**
 * Возвращает список авторов (для админки)
 **/
function get_authors_list() {
	$authors = App\Author::pluck('name', 'id')->toArray();

	$authors[0] = 'Не указано';

	ksort($authors);

	return $authors;
}

function str_file_size($file) {
	$file = trim($file, '/');

	$size = (int) filesize($file);

	if ($size < 1000) return number_format($size, 2, ',', ' ') . ' байт';

	if ($size < 1000000) return number_format($size / 1000, 2, ',', ' ') . ' Кбайт';

	if ($size < 1000000000) return number_format($size / 1000000, 2, ',', ' ') . ' Мбайт';

	if ($size < 1000000000000) return number_format($size / 1000000000, 2, ',', ' ') . ' Гбайт';
}


function getNumber($number) {
	return preg_replace("/[^\+0-9]/", '', $number);
}


function getYoutubeImage($url)
{
    $url_parts = explode('?', $url);
    parse_str(end($url_parts), $url_parsed);

    if (!isset($url_parsed['v'])) {
        return '';
    }

    $key = $url_parsed['v'];
    $image_name = 'content_uploads/youtube/' . $key . '.jpg';

    if (is_file($image_name)) return '/' . $image_name;

    $types = [
        'maxresdefault',
        'sddefault',
        'mqdefault',
        'hqdefault',
        'default',
    ];

    $image = '';

    for ($i = 0; $i < count($types); $i++) {
        $url = 'https://img.youtube.com/vi/' . $key . '/' . $types[$i] . '.jpg';
        $image = file_get_contents($url);

        if ($image) break;
    }

    if ($image) {
        file_put_contents($image_name, $image);

        return '/' . $image_name;
    }

    return '';
}
