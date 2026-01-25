import { usePage } from '@inertiajs/vue3';

export function useTrans() {
    return {
        trans: (key: string, replacements: Record<string, string> = {}) => {
            const page = usePage();
            const translations = (page.props.translations as Record<string, string>) || {};

            let translation = translations[key] || key;

            Object.keys(replacements).forEach((replace) => {
                translation = translation.replace(`:${replace}`, replacements[replace]);
            });

            return translation;
        },
        // Alias commonly used in Laravel
        __: (key: string, replacements: Record<string, string> = {}) => {
            const page = usePage();
            const translations = (page.props.translations as Record<string, string>) || {};

            let translation = translations[key] || key;

            Object.keys(replacements).forEach((replace) => {
                translation = translation.replace(`:${replace}`, replacements[replace]);
            });

            return translation;
        }
    };
}
