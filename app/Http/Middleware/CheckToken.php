<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class CheckToken
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
         //@TODO Убрать заглушку

        return $next($request);

        if (!$request->hasHeader('Authorization')) {
            abort(403, 'Доступ запрещен');
        }

        $token = $request->header('Authorization');

        $user = User::where('token', $token)->get()->first();

        if (!isset($user['id'])) {
            abort(403, 'Доступ запрещен');

        }

        return $next($request);
    }
}
