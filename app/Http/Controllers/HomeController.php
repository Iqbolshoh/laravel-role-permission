<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    // Bosh sahifani ko'rsatish uchun method
    public function index()
    {
        return view('welcome'); // resources/views/welcome.blade.php
    }
}
