<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Subscriber;
use App\Settings;
use Mail;
use App\News;
use App\Article;
use App\Job;
use Illuminate\Support\Facades\Cache;
use App\IPB;

class MailController extends Controller
{
	protected $private_key = '6Lch4ggUAAAAAKFb5eXfPGohmYvuRIUIeArxM_ed';

	public function email() {
		$news = News::published()->take(8)->get();
		$articles = Article::published()->take(3)->get();
        $email_popular_forums = Settings::byKey('email_popular_forums')->value;

        
        $new_forums = Cache::get('forum.email_new_forums', function () use ($forum_on_main) {
            $forums = [];
            $ids = explode(',', trim(Settings::getByKey('email_new_forums'), ','));

            foreach($ids as $id) {
				$f = IPB::api('forums/topics/' . $id);
				if (empty($f['errorCode'])) {
					$forums[] = $f;
				}
            }

            Cache::add('forum.email_new_forums', $forums, 60);
            return $forums;
        }, 60);



        $popular_forums = Cache::get('forum.email_popular_forums', function () use ($forum_on_main) {
            $forums = [];
            $ids = explode(',', trim(Settings::getByKey('email_popular_forums'), ','));

            foreach($ids as $id) {
				$f = IPB::api('forums/topics/' . $id);
				if (empty($f['errorCode'])) {
					$forums[] = $f;
				}
            }

            Cache::add('forum.email_popular_forums', $forums, 60);
            return $forums;
        }, 60);

        // dd($new_forums);

		$fixed = Job::published()->fixed()->latest()->take(6)->get();
		$jobs = Job::published()->nofixed()->latest()->take(6 - $fixed->count())->get();

		$jobs = $fixed->concat($jobs);

		return view('layouts.email', compact(
			'news',
			'articles',
            'new_forums',
            'popular_forums',
            'jobs'
		));
	}

    public function subscribe(Request $request)
    {
    	$email = $request->get('email');

    	$validator = Validator::make(['email' => $email], [
            'email' => 'required|email|unique:subscribers',
        ]);

		if ($validator->fails()) {
            return [
            	'title' => 'Ошибка',
            	'text' => 'Указан некорректный email, или email уже существует'
            ];
        }

    	$subscriber = Subscriber::create(['email' => $email]);

    	$data = ['email' => $email];

		 // Mail::send('email.subscribe_notify', $data, function($message) use($email) {
		 // 	$to = Settings::byKey('email')->value;
		 //
		 // 	$message->to($to)->subject('Подписка на новости');
		 // });

		$data = ['subscriber' => $subscriber];

		Mail::send('email.subscribe_confirm', $data, function($message) use($subscriber) {
			$message->to($subscriber->email)->subject('Подтверждение email');
			//$message->from($subscriber);
		});

		$subscribe_email_message = Settings::getByKey('subscribe_email_message');
		$subscribe_email_message = str_replace('{{email}}', $email, $subscribe_email_message);

		return [
        	'title' => 'Спасибо',
        	'text' => $subscribe_email_message
        ];
    }

	public function subscribe_confirm($code)
	{
		$subscriber = Subscriber::where(['confirm_code' => $code, 'confirmed' => null])->first();

		if (!$subscriber) {
			return abort(404);
		}

		$subscriber->confirmed = 1;
		$subscriber->save();

		Mail::send('email.subscribe_notify', ['email' => $subscriber->email], function($message) use($subscriber) {
		   $to = Settings::byKey('email')->value;

		   $message->to($to)->subject('Подписка на новости');
		});

		return view('subscribe_confirm');
	}

    public function feedback(Request $request)
    {
    	$data = $request->all();

    	$validator = Validator::make($data, [
            'email' => 'required|email',
            'msg' => 'required',
        ]);

        $recaptcha = $request->get('g-recaptcha-response');

        if (!check_recaptcha($recaptcha, $this->private_key)) {
            return [
                'title' => 'Ошибка',
                'text' => 'Подтвердите что вы не робот'
            ];
        }

        if ($validator->fails()) {
			$error_msg  = $validator->errors()->has('email') ? 'Указан некорректный email.' : '';
			$error_msg .= $validator->errors()->has('msg') ? ' Не заполнено поле сообщение.' : '';
            return [
            	'title' => 'Ошибка',
            	'text' => $error_msg
            ];
        }

    	try {
    		Mail::send('email.feedback', $data, function($message) use($data) {
				$to = Settings::byKey('email')->value;

				$message->to($to)->subject('Обратная связь');
				$message->from($data['email']);
			});
    	} catch (Exception $e) {
    		return [
	        	'title' => 'Ошибка',
	        	'text' => 'Попробуйте выполнить запрос позже'
	        ];
    	}


		return [
        	'title' => 'Спасибо',
        	'text' => 'Сообщение успешно отправлено'
        ];
    }

	public function jobEmail(Request $request)
	{
		$email = $request->get('email');
		$name  = $request->get('name');
		$phone = $request->get('phone');
		$page  = $request->get('page');
		$job_name  = $request->get('job_name');
		$introtext  = $request->get('introtext');

    	$validator = Validator::make(['email' => $email], [
            'email' => 'required|email',
        ]);

        $recaptcha = $request->get('g-recaptcha-response');

        if (!check_recaptcha($recaptcha, $this->private_key)) {
            return [
                'title' => 'Ошибка',
                'text' => 'Подтвердите что вы не робот'
            ];
        }

        if ($validator->fails()) {
            return [
            	'title' => 'Ошибка',
            	'text' => 'Указан некорректный email'
            ];
        }

    	$subscriber = Subscriber::create(['email' => $email]);

    	$data = [
			'email' => $email,
			'name' => $name,
			'phone' => $phone,
			'page' => $page,
			'job_name' => $job_name,
			'introtext' => $introtext,
		];

		Mail::send('email.job', $data, function($message) use($email) {
			$to = Settings::byKey('job_email')->value;

			$message->to($to)->subject('Отклик на резюме');
			$message->from($email);
		});

		return [
        	'title' => 'Спасибо',
        	'text' => 'Заявка успешно отправлена'
        ];
	}
}
