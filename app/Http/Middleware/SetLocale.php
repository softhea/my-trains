<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->query('lang', Cookie::get('locale'));

        if (is_string($locale) && in_array($locale, ['en', 'ro'], true)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}


