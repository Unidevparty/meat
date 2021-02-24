<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\IPB;

class AuthController extends Controller
{
    public function login(Request $request)
    {
    	$login = $request->get('login');
    	$password = $request->get('password');

    	//IPB::login($login, $password);

    	if (IPB::login($login, $password)) {
    		return redirect()->back();
    	}
    	

    	return redirect()->back()->withErrors(['auth' => 'error']);;
    }

    public function logout()
    {
    	IPB::logout();

        return redirect(route('home'));
    }
}
