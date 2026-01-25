<script setup lang="ts">
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <GuestLayout>

        <Head title="Welcome" />

        <div class="container mx-auto py-16">
            <h1 class="text-4xl font-bold">Welcome to {{ $page.props.appName }}</h1>
            <!-- your hero / content -->
        </div>
    </GuestLayout>
</template>
