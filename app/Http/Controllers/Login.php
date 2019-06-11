<?php

namespace App\Http\Controllers;

use App\Util;
use Illuminate\Http\Request;
use Session;

class Login extends Controller
{
    public function index()
    {
        return view('login', [
            'login_id' => null,
            'password' => null,
            'target' => '/',
        ]);
    }

    public function login(Request $request)
    {
        $config = config('secret.login');
        $loginId = $request->post('login_id');
        $password = $request->post('password');

        if (
            hash('SHA256', $loginId) == $config['id'] &&
            hash('SHA256', $password) == $config['password']
        ) {
            // login ok
            Session::put('login', [
                'login' => $loginId,
                'expire' => time() + 60 * 120
            ]);
            return redirect($request->post('target'));
        } else {
            Util::setMessage('error', 'Login failure');
            return view('login', $request->post());
        }
    }

    public function logout()
    {
        Session::remove('login');
        return redirect('/');
    }
}
