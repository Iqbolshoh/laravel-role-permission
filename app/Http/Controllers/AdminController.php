<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Ensure only admins can access this controller.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Show the admin dashboard.
     */
    public function index()
    {
        return view('admin.dashboard'); // Ensure you create this Blade file: resources/views/admin/dashboard.blade.php
    }
}
