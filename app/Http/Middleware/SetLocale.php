<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
     public function handle(Request $request, Closure $next)
    {
        logger('Locale from session: ' . session('locale'));
        // 1️⃣ Session has highest priority
        if (session()->has('locale')) {
            $locale = session('locale');
        }
        // 2️⃣ Otherwise use app default
        else {
            $locale = config('app.locale');
        }

        // 3️⃣ Safety check
        if (! in_array($locale, config('app.locales'))) {
            $locale = config('app.fallback_locale');
        }

        App::setLocale($locale);

        return $next($request);
    }
}

