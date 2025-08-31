<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireEmailVerification
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to perform this action.');
        }

        $user = auth()->user();

        if (!$user->isVerified()) {
            return redirect()->back()->with('error', 'You must verify your email address before you can add products or place orders. Please check your email for a verification link.');
        }

        return $next($request);
    }
}
