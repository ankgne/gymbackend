<?php

namespace App\Http\Middleware;

use App\Services\Helper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class isAdmin
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
        {
            $user = Auth::user();
            // hasRole is a laratrust method
            if ($user->hasRole('admin')) {
                return $next($request);
            }
            return response()->json(['message' => 'URL is unauthorized for your role'], 403);
        }
    }
}
