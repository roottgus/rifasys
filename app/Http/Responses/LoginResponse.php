<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        // Decide destino según rol
        $home = Auth::user()->is_admin
            ? route('admin.dashboard')
            : route('dashboard');

        if ($request->wantsJson()) {
            return new JsonResponse(['two_factor' => false]);
        }

        return redirect()->intended($home);
    }
}
