<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User\UserType;

class ClientMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            if (Auth::user()->Type()->first()->id === UserType::ID_CLIENT) {
                return $next($request);
            }
            return redirect()->route('admin.home.index');
        }
        return redirect()->route('login');
    }
}