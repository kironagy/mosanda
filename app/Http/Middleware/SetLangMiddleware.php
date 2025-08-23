<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLangMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasHeader('Accept-Language')) {
            $languages = explode(',', $request->header('Accept-Language'));
            $supportedLocales = ['ar', 'en']; // Define allowed locales

            foreach ($languages as $lang) {
                $lang = trim(explode(';', $lang)[0]); // Extract language without priority (e.g., "en;q=0.8" → "en")

                if (in_array($lang, $supportedLocales)) {
                    app()->setLocale($lang);
                    break;
                }
            }
        }else{
            app()->setLocale('en');
        }
        return $next($request);
    }

}
