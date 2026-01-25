<?php

namespace App\Helpers;

class LocaleHelper
{
    /**
     * Get current locale
     */
    public static function getCurrentLocale(): string
    {
        return app()->getLocale();
    }

    /**
     * Get default locale
     */
    public static function getDefaultLocale(): string
    {
        return config('locale.default_locale', 'en');
    }

    /**
     * Get supported locales
     */
    public static function getSupportedLocales(): array
    {
        return config('locale.supported_locales', ['en']);
    }

    /**
     * Check if locale should be hidden in URL
     */
    public static function shouldHideDefaultLocale(): bool
    {
        return config('locale.hide_default_locale_in_url', true);
    }

    /**
     * Get locale name
     */
    public static function getLocaleName(string $locale): string
    {
        return config("locale.locale_names.{$locale}", $locale);
    }

    /**
     * Switch locale URL
     */
    public static function switchLocaleUrl(string $locale): string
    {
        $currentPath = request()->path();
        $currentLocale = self::getCurrentLocale();
        $defaultLocale = self::getDefaultLocale();
        $hideDefault = self::shouldHideDefaultLocale();

        // Extract path without locale
        $pathWithoutLocale = $currentPath;
        foreach (self::getSupportedLocales() as $loc) {
            if (str_starts_with($pathWithoutLocale, "{$loc}/")) {
                $pathWithoutLocale = substr($pathWithoutLocale, strlen("{$loc}/"));
                break;
            } elseif ($pathWithoutLocale === $loc) {
                $pathWithoutLocale = '';
                break;
            }
        }

        // Build new URL
        if ($hideDefault && $locale === $defaultLocale) {
            return url($pathWithoutLocale ?: '/');
        } else {
            return url($pathWithoutLocale ? "{$locale}/{$pathWithoutLocale}" : "/{$locale}");
        }
    }
}
