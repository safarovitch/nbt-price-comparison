<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="d-flex h-100 flex-fill flex-column gap-3 overflow-x-auto rounded p-3"
        >
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div
                        class="position-relative overflow-hidden rounded border"
                        style="aspect-ratio: 16 / 9;"
                    >
                        <PlaceholderPattern />
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div
                        class="position-relative overflow-hidden rounded border"
                        style="aspect-ratio: 16 / 9;"
                    >
                        <PlaceholderPattern />
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div
                        class="position-relative overflow-hidden rounded border"
                        style="aspect-ratio: 16 / 9;"
                    >
                        <PlaceholderPattern />
                    </div>
                </div>
            </div>
            <div
                class="position-relative flex-fill rounded border"
                style="min-height: 100vh;"
            >
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
