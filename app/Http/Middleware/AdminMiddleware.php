<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User\UserType;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            if (Auth::user()->Type()->first()->id === UserType::ID_ADMIN) {
                return $next($request);
            }
            return redirect()->route('client.home.index');
        }
        return redirect()->route('login');
    }
}