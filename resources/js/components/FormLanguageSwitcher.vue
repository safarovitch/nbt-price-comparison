<script setup lang="ts">
import { computed } from 'vue';

interface Language {
    code: string;
    label: string;
    flag?: string;
}

const props = defineProps<{
    modelValue: string; // The current active language code
    languages: Language[];
    // Optional: Pass a function or object to check which languages have errors or content
    hasContent?: (langCode: string) => boolean;
    hasError?: (langCode: string) => boolean;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const currentLang = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});
</script>

<template>
    <div class="d-flex align-items-center justify-content-end mb-3">
        <div class="p-1 bg-light rounded d-flex gap-1 border">
            <button v-for="lang in languages" :key="lang.code" type="button" class="btn btn-sm d-flex align-items-center gap-2 position-relative" :class="currentLang === lang.code ? 'btn-white shadow-sm text-primary fw-bold' : 'text-muted hover-bg-gray'" @click="currentLang = lang.code" style="min-width: 100px; justify-content: center;">
                <span v-if="lang.flag" class="fs-6">{{ lang.flag }}</span>
                <span>{{ lang.label }}</span>

                <!-- Indicators -->
                <span v-if="hasError && hasError(lang.code)" class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="width: 8px; height: 8px;"></span>
                <span v-else-if="hasContent && hasContent(lang.code) && currentLang !== lang.code" class="position-absolute top-0 start-100 translate-middle p-1 bg-success border border-light rounded-circle" style="width: 8px; height: 8px;"></span>
            </button>
        </div>
    </div>
</template>

<style scoped>
.btn-white {
    background-color: #fff;
    border: 1px solid #e5e7eb;
}

.hover-bg-gray:hover {
    background-color: #f3f4f6;
}
</style>
