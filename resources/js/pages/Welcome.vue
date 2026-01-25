<script setup lang="ts">
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>

    <Head title="Welcome" />

    <GuestLayout>
        <div class="d-flex h-100 flex-fill flex-column gap-3 overflow-x-auto rounded p-3">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="position-relative overflow-hidden rounded border" style="aspect-ratio: 16 / 9;">
                        <h1>Welcome</h1>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="position-relative overflow-hidden rounded border" style="aspect-ratio: 16 / 9;">
                        <h1>Welcome</h1>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="position-relative overflow-hidden rounded border" style="aspect-ratio: 16 / 9;">
                        <h1>Welcome</h1>
                    </div>
                </div>
            </div>
            <div class="position-relative flex-fill rounded border" style="min-height: 100vh;">
                <h1>Welcome</h1>
            </div>
        </div>
    </GuestLayout>

</template>
