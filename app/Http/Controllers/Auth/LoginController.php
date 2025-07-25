<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Determina a dónde redirigir tras login.
     */
    protected function redirectTo()
    {
        // Asumimos que en tu tabla de users hay un atributo 'is_admin'
        if (Auth::user()->is_admin) {
            return route('admin.dashboard');    // /admin/dashboard
        }

        return route('dashboard');              // /dashboard
    }

    // Si prefieres más control, puedes usar authenticated():
    /*
    protected function authenticated(Request $request, $user)
    {
        if ($user->is_admin) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('dashboard');
    }
    */
}
