<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;
class RedirectIfNotAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         $admin = Auth::user();

        if (!$admin || !$admin->hasRole('Admin')) {
            return redirect()->route('admin.login')->withErrors(['error' => 'Access denied for non-admins.']);
        }

        return $next($request);
    }
}
