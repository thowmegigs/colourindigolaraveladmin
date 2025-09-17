<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;
class RedirectIfNotVendor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         $v= Auth::guard('vendor')->user();

        if (!$v|| !$v->hasRole('Vendor')) {
            return redirect()->route('vendor.login')->withErrors(['error' => 'Access denied for non-admins.']);
        }

        return $next($request);
    }
}
