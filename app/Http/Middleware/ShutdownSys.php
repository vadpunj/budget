<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\User;
use Func;

class ShutdownSys
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
      $shut = Func::rang_shutdown(date('Y-m-d'));
      if(Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isUser() || Auth::user()->isApprove2()) && $shut == false){
          return redirect('close');
      }else{
        return $next($request);
      }

    }
}
