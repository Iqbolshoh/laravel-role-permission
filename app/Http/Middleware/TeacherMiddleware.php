<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TeacherMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === 'teacher') {
            return $next($request);
        }

        return redirect('/')->with('error', 'Siz o‘qituvchi emassiz!');
    }
}
