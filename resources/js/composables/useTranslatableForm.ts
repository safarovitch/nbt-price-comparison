import { ref } from 'vue';

export interface Language {
    code: string;
    label: string;
    flag?: string;
}

export function useTranslatableForm(initialLang: string = 'ru') {
    const currentLang = ref(initialLang);

    const availableLanguages: Language[] = [
        { code: 'ru', label: 'Русский', flag: '🇷🇺' },
        { code: 'en', label: 'English', flag: '🇬🇧' },
        { code: 'tj', label: 'Тоҷикӣ', flag: '🇹🇯' },
    ];

    // Helper to sync field value to other empty languages on blur
    // This helps quickly populate fields if they are the same (e.g. names) or to avoid empty required fields
    const syncField = (form: any, fieldName: string) => {
        const currentValue = form[fieldName][currentLang.value];
        
        if (currentValue && typeof currentValue === 'string' && currentValue.trim()) {
            availableLanguages.forEach(lang => {
                // If the other language is empty, fill it with the current value
                if (lang.code !== currentLang.value) {
                    const otherValue = form[fieldName][lang.code];
                    if (!otherValue || (typeof otherValue === 'string' && !otherValue.trim())) {
                        form[fieldName][lang.code] = currentValue;
                    }
                }
            });
        }
    };

    // Helper to check if a specific field has content in a specific language
    const hasContent = (form: any, fieldName: string, langCode: string): boolean => {
        const value = form[fieldName]?.[langCode];
        if (!value) return false;
        if (typeof value === 'string') return value.trim().length > 0;
        return true; // Non-string/non-null values considered "content"
    };

    return {
        currentLang,
        availableLanguages,
        syncField,
        hasContent
    };
}
