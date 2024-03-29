<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\User;

class ApproveAuth
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
      if(Auth::check() && (Auth::user()->isSuperAdmin() || Auth::user()->isApprove2() || Auth::user()->isApproveAll())){
          return $next($request);
      }
      return redirect('home');
    }
}
