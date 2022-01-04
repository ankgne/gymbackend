<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class isMember
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        // hasRole is a laratrust method
        if ($user->hasRole('member')) {
            return $next($request);
        }
        return response()->json(['message' => 'URL is unauthorized for your role'], 403);
    }
}
