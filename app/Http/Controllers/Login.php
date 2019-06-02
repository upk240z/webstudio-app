<?php

namespace App\Http\Controllers;

use App\Util;
use Illuminate\Http\Request;

class Login extends Controller
{
    public function index(Request $request)
    {
        return view('login', [
            '_login' => null,
            '_pwd' => null,
        ]);
    }

    public function post(Request $request)
    {
        $login = $request->post('_login');
        $password = $request->post('_pwd');

        $config = config('secret.login');

        if (
            hash('SHA256', $login) == $config['id'] &&
            hash('SHA256', $password) == $config['password']
        ) {
            \Session::put('login', [
                'login' => $login,
                'expire' => time() + 60 * 120
            ]);

            return redirect('/');
        } else {
            Util::setMessage('error', 'Login faiure');
        }

        return view('login', [
            '_login' => $login,
            '_pwd' => $password,
        ]);
    }

    public function logout(Request $request)
    {
        \Session::remove('login');
        return redirect('/login');
    }
}
