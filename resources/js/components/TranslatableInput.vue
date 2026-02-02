<script setup lang="ts">
import { ref, computed, watch } from 'vue';

interface TranslatableValue {
    ru?: string | null;
    en?: string | null;
    tj?: string | null;
}

const props = defineProps<{
    modelValue: TranslatableValue | string | null;
    label: string;
    type?: 'input' | 'textarea';
    placeholder?: string;
    required?: boolean;
    rows?: number;
    error?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: TranslatableValue): void;
}>();

const languages = [
    { code: 'ru', label: 'Русский', flag: '🇷🇺' },
    { code: 'en', label: 'English', flag: '🇬🇧' },
    { code: 'tj', label: 'Тоҷикӣ', flag: '🇹🇯' },
];

const activeTab = ref('ru');

// Normalize incoming value to TranslatableValue format
const normalizedValue = computed<TranslatableValue>(() => {
    if (!props.modelValue) {
        return { ru: '', en: '', tj: '' };
    }
    if (typeof props.modelValue === 'string') {
        return { ru: props.modelValue, en: '', tj: '' };
    }
    return {
        ru: props.modelValue.ru ?? '',
        en: props.modelValue.en ?? '',
        tj: props.modelValue.tj ?? '',
    };
});

const updateValue = (lang: string, value: string) => {
    emit('update:modelValue', {
        ...normalizedValue.value,
        [lang]: value || null,
    });
};

const getValue = (lang: string): string => {
    return (normalizedValue.value as any)[lang] ?? '';
};
</script>

<template>
    <div class="translatable-input">
        <label v-if="label" class="form-label fw-semibold d-flex align-items-center gap-2">
            {{ label }}
            <span v-if="required" class="text-danger">*</span>
        </label>

        <!-- Language Tabs -->
        <ul class="nav nav-tabs nav-tabs-sm mb-2">
            <li v-for="lang in languages" :key="lang.code" class="nav-item">
                <button type="button" class="nav-link py-1 px-2 d-flex align-items-center gap-1" :class="{ 'active': activeTab === lang.code }" @click="activeTab = lang.code">
                    <span>{{ lang.flag }}</span>
                    <span class="small">{{ lang.label }}</span>
                    <span v-if="getValue(lang.code)" class="badge bg-success-subtle text-success ms-1" style="font-size: 0.6rem;">✓</span>
                </button>
            </li>
        </ul>

        <!-- Input Fields -->
        <div v-for="lang in languages" :key="lang.code">
            <div v-show="activeTab === lang.code">
                <textarea v-if="type === 'textarea'" :value="getValue(lang.code)" @input="updateValue(lang.code, ($event.target as HTMLTextAreaElement).value)" class="form-control" :rows="rows || 3" :placeholder="`${placeholder || ''} (${lang.label})`" :required="required && lang.code === 'ru'"></textarea>
                <input v-else :value="getValue(lang.code)" @input="updateValue(lang.code, ($event.target as HTMLInputElement).value)" type="text" class="form-control" :placeholder="`${placeholder || ''} (${lang.label})`" :required="required && lang.code === 'ru'" />
            </div>
        </div>

        <div v-if="error" class="text-danger small mt-1">{{ error }}</div>
    </div>
</template>

<style scoped>
.nav-tabs-sm .nav-link {
    font-size: 0.85rem;
}

.translatable-input .nav-tabs {
    border-bottom: 1px solid #dee2e6;
}

.translatable-input .nav-link {
    border: none;
    border-bottom: 2px solid transparent;
    border-radius: 0;
    color: #6c757d;
}

.translatable-input .nav-link.active {
    border-bottom-color: #0d6efd;
    color: #0d6efd;
    background: transparent;
}

.translatable-input .nav-link:hover:not(.active) {
    border-bottom-color: #dee2e6;
}
</style>
