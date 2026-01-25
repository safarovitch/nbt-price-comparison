<script setup lang="ts">
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const isOpen = ref(false);
const dropdownRef = ref<HTMLElement | null>(null);

const currentLocale = computed(() => page.props.locale || 'en');

const supportedLocales = computed(() => {
    const locales = page.props.supportedLocales || ['en'];
    const localeNames = page.props.localeNames || {
        en: 'English',
        ru: 'Русский',
        tj: 'Тоҷикӣ',
    };

    return locales.map((locale: string) => ({
        code: locale,
        name: localeNames[locale] || locale,
        isActive: locale === currentLocale.value,
    }));
});

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};

const switchLocale = (locale: string) => {
    if (locale === currentLocale.value) {
        isOpen.value = false;
        return;
    }

    isOpen.value = false;
    router.visit(`/locale/${locale}`, {
        preserveState: true,
        preserveScroll: true,
        only: [],
    });
};

const closeOnClickOutside = (event: MouseEvent) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
        isOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', closeOnClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', closeOnClickOutside);
});
</script>

<template>
    <div class="language-switcher" ref="dropdownRef">
        <div class="dropdown">
            <button
                class="dropdown-toggle"
                type="button"
                @click="toggleDropdown"
                :aria-label="`Current language: ${currentLocale}`"
                :aria-expanded="isOpen"
            >
                <span class="current-locale">{{ currentLocale.toUpperCase() }}</span>

            </button>
            <div class="dropdown-menu" :class="{ 'show': isOpen }">
                <button
                    v-for="locale in supportedLocales"
                    :key="locale.code"
                    @click="switchLocale(locale.code)"
                    :class="['dropdown-item', { active: locale.isActive }]"
                    type="button"
                >
                    {{ locale.name }}
                    <span v-if="locale.isActive" class="check">✓</span>
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.language-switcher {
    position: relative;
    z-index: 10001;
}

.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-toggle {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: transparent;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    color: #1a1a1a;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-right: 10px;
}

.dropdown-toggle:hover {
    border-color: var(--accent-color);
    background: #f8f9fa;
}

.dropdown-toggle svg {
    transition: transform 0.3s ease;
}

.current-locale {
    font-weight: 600;
}

.dropdown-menu {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    min-width: 150px;
    background: #ffffff;
    border: 1px solid #f0f0f0;
    border-radius: 12px;
    padding: 8px 0;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    z-index: 10000;
}

.dropdown-menu.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
    pointer-events: auto;
}

svg.rotate {
    transform: rotate(180deg);
}

.dropdown-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 10px 16px;
    background: transparent;
    border: none;
    color: #333;
    font-size: 14px;
    text-align: left;
    cursor: pointer;
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background: #f8f9fa;
    color: var(--primary-color);
}

.dropdown-item.active {
    background: #f0f0f0;
    color: var(--primary-color);
    font-weight: 600;
}

.check {
    margin-left: 8px;
    font-weight: bold;
    color: var(--accent-color);
}
</style>
