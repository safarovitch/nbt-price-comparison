<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import organizations from '@/routes/admin/organizations';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import OrganizationForm from './Partials/OrganizationForm.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Organizations',
        href: organizations.index().url,
    },
    {
        title: 'Add Organization',
        href: organizations.create().url,
    },
];

const submit = (form: any) => {
    form.post(organizations.store().url);
};
</script>

<template>
    <Head title="Add Organization" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4" style="max-width: 800px;">
            <div class="mb-4">
                <h2 class="h4 mb-1">Add Organization</h2>
                <p class="text-muted small">Create a new organization and configure its API settings.</p>
            </div>

            <div class="card shadow-sm border-0 p-4">
                <OrganizationForm submit-label="Create Organization" :on-submit="submit" />
            </div>
        </div>
    </AppLayout>
</template>
