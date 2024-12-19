<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

class Permission
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::user()->hasPermissionTo(Route::currentRouteName())) {
            return abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
