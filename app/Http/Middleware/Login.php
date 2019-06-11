<?php

namespace App\Http\Middleware;

use App\Util;
use Closure;
use Illuminate\Support\Facades\URL;

class Login
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $loginId = null;
        $password = null;
        if ($request->post('login_form')) {
            $config = config('secret.login');
            $loginId = $request->post('_login');
            $password = $request->post('_pwd');

            if (
                hash('SHA256', $loginId) == $config['id'] &&
                hash('SHA256', $password) == $config['password']
            ) {
                // login ok
                \Session::put('login', [
                    'login' => $loginId,
                    'expire' => time() + 60 * 120
                ]);
                return redirect(URL::current());
            } else {
                Util::setMessage('error', 'Login failure');
            }
        }

        // session check
        $login = \Session::get('login');

        if (
            !is_array($login) ||
            !isset($login['expire']) ||
            $login['expire'] < time()
        ) {
            return \Response::view('login', [
                '_login' => $loginId,
                '_pwd' => $password,
            ]);
        }

        $login['expire'] = time() + 60 * 120;
        \Session::put('login', $login);

        return $next($request);
    }

}
