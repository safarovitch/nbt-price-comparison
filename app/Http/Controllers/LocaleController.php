<?php

namespace App\Http\Controllers;

use App\Helpers\LocaleHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Switch locale and redirect to same page
     */
    public function switch(Request $request, string $new_locale): RedirectResponse
    {
        $supportedLocales = LocaleHelper::getSupportedLocales();

        if (!in_array($new_locale, $supportedLocales)) {
            abort(404);
        }

        session(['locale' => $new_locale]);
        app()->setLocale($new_locale);

        // Get referrer or current path
        $referer = $request->header('referer');
        $defaultLocale = LocaleHelper::getDefaultLocale();
        $hideDefault = LocaleHelper::shouldHideDefaultLocale();

        if ($referer) {
            // Parse the referer URL
            $parsedUrl = parse_url($referer);
            $refererPath = $parsedUrl['path'] ?? '/';

            // Remove locale from referer path
            $pathWithoutLocale = $refererPath;
            foreach ($supportedLocales as $loc) {
                if (str_starts_with($pathWithoutLocale, "/{$loc}/")) {
                    $pathWithoutLocale = substr($pathWithoutLocale, strlen("/{$loc}/"));
                    break;
                } elseif ($pathWithoutLocale === "/{$loc}") {
                    $pathWithoutLocale = '/';
                    break;
                }
            }

            // Remove leading slash if it's not root
            if ($pathWithoutLocale !== '/' && str_starts_with($pathWithoutLocale, '/')) {
                $pathWithoutLocale = substr($pathWithoutLocale, 1);
            }

            // Build new path with new locale
            if ($hideDefault && $new_locale === $defaultLocale) {
                $newPath = $pathWithoutLocale === '/' || empty($pathWithoutLocale) ? '/' : "/{$pathWithoutLocale}";
            } else {
                $newPath = $pathWithoutLocale === '/' || empty($pathWithoutLocale) ? "/{$new_locale}" : "/{$new_locale}/{$pathWithoutLocale}";
            }

            return redirect($newPath);
        }

        // Fallback: if no referer, redirect to locale root
        if ($hideDefault && $new_locale === $defaultLocale) {
            return redirect('/');
        }

        return redirect("/{$new_locale}");
    }
}
