<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Login extends Controller
{
    public function logout(Request $request)
    {
        \Session::remove('login');
        return redirect('/');
    }
}
