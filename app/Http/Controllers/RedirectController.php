<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User\UserType;

class RedirectController extends Controller
{
    public function redirectToHome()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->Type()->first()->id === UserType::ID_ADMIN) {
                return redirect()->route('admin.downloads.index');
            }
            return redirect()->route('client.downloads.index');
        } else {
            return redirect()->route('login');
        }
    }
}