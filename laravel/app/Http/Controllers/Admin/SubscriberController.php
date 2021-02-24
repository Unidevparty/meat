<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use View;

class SubscriberController extends Controller
{
    public function __construct()
    {
        checkPermissions('subscribers');

        View::share('section_name', 'Подписчики');
        View::share('page_name', '');
    }

    public function index(Request $request)
    {
        View::share('page_name', 'Список');

        $confirmed = $request->get('confirmed', false);
        $subscribers = null;

        if ($confirmed !== false) {
            $subscribers = Subscriber::where('confirmed', $confirmed? 1: null)->paginate();
        } else {
            $subscribers = Subscriber::paginate();
        }


        return view('admin.subscriber.list', compact('subscribers', 'confirmed'));
    }

    public function create()
    {
        View::share('page_name', 'Добавление подписчика');

        return view('admin.subscriber.form');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email'
        ]);

        $subscriber = Subscriber::create($request->all());

        flash('Подписчик успешно добавлен', 'success');

        return redirect(route('subscriber.edit', $subscriber->id));
    }

    public function edit(Subscriber $subscriber)
    {
        View::share('page_name', 'Обновление подписчика');

        return view('admin.subscriber.form', compact('subscriber'));
    }

    public function update(Request $request, Subscriber $subscriber)
    {
        $this->validate($request, [
            'email' => 'required|email'
        ]);

        $subscriber->update($request->all());

        flash('Подписчик успешно обновлен', 'success');

        return redirect(route('subscriber.edit', $subscriber->id));
    }

    public function destroy(Subscriber $subscriber)
    {
        $subscriber->delete();

        flash('Подписчик успешно удален', 'success');

        return redirect()->back();
    }

	public function download(Request $request)
	{
		$headers = [
			'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
			'Content-type' => 'text/csv',
			'Content-Disposition' => 'attachment; filename=subscribers.csv',
			'Expires' => '0',
			'Pragma' => 'public'
		];

        $confirmed = $request->get('confirmed', false);

        $subscribers = null;

        if ($confirmed === false) {
            $subscribers = Subscriber::all()->toArray();
        } else {
            $subscribers = Subscriber::where('confirmed', $confirmed? 1: null)->get()->toArray();
        }

		# add headers for each column in the CSV download
		array_unshift($subscribers, array_keys($subscribers[0]));

		$data = '';
		foreach ($subscribers as $row) {
			$data .= str_putcsv($row, ';') . "\n";
		}

	    return \Response::make($data, 200, $headers);
	}

    public function search(Request $request)
    {
        $email = $request->get('email');

        $subscribers = Subscriber::where('email', 'like', '%' . $email . '%')->paginate();

        return view('admin.subscriber.list', compact('subscribers'));
    }
}
