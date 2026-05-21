<?php

namespace App\Http\Controllers;

class HalamanController extends Controller
{
    public function index()
    {
        return 'Ini halaman dari controller';
    }

    public function profil()
    {
        return view('profil');
    }
}
