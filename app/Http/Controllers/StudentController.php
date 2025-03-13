<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        return view('student.dashboard'); // resources/views/student/dashboard.blade.php
    }
}
