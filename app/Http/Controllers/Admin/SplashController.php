<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SplashController extends Controller
{
    public function index()
    {
        // Aquí podrías pasar datos (logo, nombre de la app, etc).
        return view('admin.splash');
    }
}
