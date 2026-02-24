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
        <div class="d-flex w-100 flex-column gap-4" style="max-width: 28rem;">
            <Link
                :href="index()"
                class="d-flex align-items-center gap-2 align-self-center fw-medium text-decoration-none"
            >
                <div class="d-flex align-items-center justify-content-center" style="height: 3rem;">
                    <AppLogoIcon
                        style="height: 3rem;"
                    />
                </div>
            </Link>

            <div class="d-flex flex-column gap-4">
                <div class="card rounded-3 shadow-sm">
                    <div class="card-header bg-transparent border-0 px-5 pt-4 pb-0 text-center">
                        <h5 class="card-title mb-2">{{ title }}</h5>
                        <p class="card-text text-muted small mb-0">
                            {{ description }}
                        </p>
                    </div>
                    <div class="card-body px-5 py-4">
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
