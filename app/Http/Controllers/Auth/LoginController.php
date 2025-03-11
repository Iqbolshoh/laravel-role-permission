<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Redirect users based on their role after login.
     *
     * @return string
     */
    protected function redirectTo()
    {
        if (Auth::user()->role === 'admin') {
            return '/admin/dashboard';
        } elseif (Auth::user()->role === 'teacher') {
            return '/teacher/dashboard';
        }
        return '/home'; // Default fallback
    }

    /**
     * Logout and redirect to the login page.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/login');
    }

    /**
     * Create a new instance of LoginController.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
