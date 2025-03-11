<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeacherController extends Controller
{
    /**
     * Ensure only teachers can access this controller.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'teacher']);
    }

    /**
     * Show the teacher dashboard.
     */
    public function index()
    {
        return view('teacher.dashboard'); // Ensure you create this Blade file: resources/views/teacher/dashboard.blade.php
    }
}
