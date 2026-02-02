<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = config('locale.supported_locales', ['en']);
        $defaultLocale = config('locale.default_locale', 'en');
        $hideDefaultLocale = config('locale.hide_default_locale_in_url', true);

        // Get locale from route parameter (if using {locale?} in routes)
        $locale = $request->route('locale');

        // If no locale in route, check URL segment (fallback)
        if (!$locale) {
            $locale = $request->segment(1);
        }

        // Validate and set locale
        if ($locale && in_array($locale, $supportedLocales)) {
            App::setLocale($locale);
            Session::put('locale', $locale);
            \Illuminate\Support\Facades\URL::defaults(['locale' => $locale]);

            // If default locale is in URL and should be hidden, redirect to remove it
            if ($hideDefaultLocale && $locale === $defaultLocale && $request->route('locale')) {
                $pathWithoutLocale = $request->path();
                // Remove locale from path
                $pathWithoutLocale = preg_replace('#^' . preg_quote($locale, '#') . '/?#', '', $pathWithoutLocale);
                $newPath = $pathWithoutLocale ?: '/';
                if ($newPath !== $request->path()) {
                    return redirect($newPath);
                }
            }
        } else {
            // No locale in URL - use session or default
            $sessionLocale = Session::get('locale', $defaultLocale);
            if (in_array($sessionLocale, $supportedLocales)) {
                App::setLocale($sessionLocale);
            } else {
                App::setLocale($defaultLocale);
                Session::put('locale', $defaultLocale);
            }

            // If we should hide default locale and current locale is not default, redirect
            if ($hideDefaultLocale) {
                $currentLocale = App::getLocale();
                if ($currentLocale !== $defaultLocale && !$request->route('locale')) {
                    $path = $request->path() === '/' ? '' : $request->path();
                    return redirect("/{$currentLocale}" . ($path ? "/{$path}" : ''));
                }
            }
        }


        // Forget the locale parameter so it's not passed to controller methods
        if ($request->route()) {
            $request->route()->forgetParameter('locale');
        }

        return $next($request);
    }
}
