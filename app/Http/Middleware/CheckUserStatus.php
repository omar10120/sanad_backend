<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // Check if user status is not active (status != 1)
            if ($user->status != 1) {
                // Logout the user
                Auth::logout();

                // Clear session
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Redirect to login with error message
                return redirect()->route('login')->withErrors([
                    'email' => trans('auth.inactive_account'),
                ]);
            }
        }

        return $next($request);
    }
}
