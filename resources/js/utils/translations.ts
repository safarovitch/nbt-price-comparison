/**
 * Utility functions for handling translatable fields from spatie/laravel-translatable.
 */

interface Translatable {
    ru?: string | null;
    en?: string | null;
    tj?: string | null;
}

/**
 * Get the translated value for a translatable field.
 * Falls back to other languages if the requested language is not available.
 * 
 * @param value - The translatable value (can be string, object, or null)
 * @param locale - The preferred locale (defaults to 'ru')
 * @returns The translated string or fallback
 */
export function getTranslation(value: Translatable | string | null | undefined, locale: string = 'ru'): string {
    if (!value) return '';
    
    // If it's already a string, return it
    if (typeof value === 'string') return value;
    
    // Try the requested locale first
    if (value[locale as keyof Translatable]) {
        return value[locale as keyof Translatable] as string;
    }
    
    // Fallback order: ru -> en -> tj
    const fallbackOrder = ['ru', 'en', 'tj'];
    for (const lang of fallbackOrder) {
        if (value[lang as keyof Translatable]) {
            return value[lang as keyof Translatable] as string;
        }
    }
    
    return '';
}

/**
 * Check if a translatable value has any translations.
 */
export function hasTranslation(value: Translatable | string | null | undefined): boolean {
    if (!value) return false;
    if (typeof value === 'string') return value.length > 0;
    return Boolean(value.ru || value.en || value.tj);
}

/**
 * Get all available translations for a field.
 */
export function getAllTranslations(value: Translatable | string | null | undefined): Translatable {
    if (!value) return { ru: null, en: null, tj: null };
    if (typeof value === 'string') return { ru: value, en: null, tj: null };
    return {
        ru: value.ru ?? null,
        en: value.en ?? null,
        tj: value.tj ?? null,
    };
}
