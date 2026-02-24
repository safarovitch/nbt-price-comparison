<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { index } from '@/routes';
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="d-flex min-vh-100 flex-column align-items-center justify-content-center gap-4 bg-light p-4 p-md-5"
    >
        <div class="w-100" style="max-width: 24rem;">
            <div class="d-flex flex-column gap-5">
                <div class="d-flex flex-column align-items-center gap-3">
                    <Link
                        :href="index()"
                        class="d-flex flex-column align-items-center gap-2 fw-medium text-decoration-none"
                    >
                        <div
                            class="mb-2 d-flex align-items-center justify-content-center"
                            style="height: 3rem;"
                        >
                            <AppLogoIcon
                                style="height: 3rem;"
                            />
                        </div>
                        <span class="visually-hidden">{{ title }}</span>
                    </Link>
                    <div class="d-flex flex-column gap-2 text-center">
                        <h1 class="h4 fw-medium mb-0">{{ title }}</h1>
                        <p class="text-center small text-muted mb-0">
                            {{ description }}
                        </p>
                    </div>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
