<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Settings;
use App\Role;
use Illuminate\Http\Request;
use View;


class SettingsController extends Controller
{

    public function __construct()
    {
        checkPermissions('settings');

        View::share('section_name', 'Настройки');
        View::share('page_name', '');
    }

    public function index()
    {
        $settings = Settings::where('key', '<>', 'signature')->get();
        return view('admin.settings', compact('settings'));
    }

    public function save(Request $request)
    {
        $data = $request->get('data');

        foreach ($data as $key => $value) {
            $setting = Settings::byKey($key);
            $setting->value = $value;
            $setting->save();
        }

        flash('Настройки успешно сохранены', 'success');

        return redirect()->back();
    }

    function roles() {
        $roles = \App\Member::ADMIN_GROUP_IDS;
        $rules  = [
            'news' => 'Новости',
            'articles' => 'Статьи',
            'interview' => 'Интервью',
            'banners' => 'Баннеры',
            'subscribers' => 'Подписчики',
			'comments' => 'Комментарии',
            'settings' => 'Настройки',
            'templates' => 'Шаблоны',
            'pages' => 'Страницы',
            'meta' => 'Метатеги',
            'companies' => 'Компании',
            'job' => 'Работа',
            'authors' => 'Авторы',
            'photogallery' => 'Фотогалерея',
            'search' => 'Поиск',
        ];

        $all_roles = Role::all();
        $saved_roles = [];

        foreach ($all_roles as $role) {
            $saved_roles[$role->rule][$role->role] = $role->value;
        }

        return view('admin.roles', compact('roles', 'rules', 'saved_roles'));
    }

    function roles_save(Request $request) {
        $data = $request->get('data');
        foreach ($data as $rule => $roles) {
            foreach ($roles as $role => $value) {
                $r = Role::firstOrNew([
                    'role' => $role,
                    'rule' => $rule
                ]);

                $r->value = $value;
                $r->save();
            }
        }
        flash('Настройки успешно сохранены', 'success');

        return redirect()->back();
    }
}
