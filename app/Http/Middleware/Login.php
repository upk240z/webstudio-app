<?php

namespace App\Http\Middleware;

use Closure;

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
        $login = \Session::get('login');

        if (
            !is_array($login) ||
            !isset($login['expire']) ||
            $login['expire'] < time()
        ) {
            return redirect('/login');
        }

        $login['expire'] = time() + 60 * 120;
        \Session::put('login', $login);

        return $next($request);
    }

}
