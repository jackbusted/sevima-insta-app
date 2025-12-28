<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        // echo('<pre>');
        // print_r($user->role_id);
        // echo('</pre>');

        if ($user->role_id === 1) {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }

    public function handle2(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role_id === 2) {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }

}
