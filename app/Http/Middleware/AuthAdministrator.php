<?php
//ไว้checkว่าเป็นสิทธิ์adminไหม ถ้าเป็นadminให้สามารถเข้าregisterได้
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\User;

class AuthAdministrator
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
        if(Auth::check() && Auth::user()->isAdmin()){
            return $next($request);
        }
        return redirect('home');
    }
}
