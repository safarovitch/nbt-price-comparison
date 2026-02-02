<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import organizations from '@/routes/admin/organizations';
import { type BreadcrumbItem, type Organization } from '@/types';
import { Head } from '@inertiajs/vue3';
import ProductForm from './Partials/ProductForm.vue';

const props = defineProps<{
    organization: Organization;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Organizations',
        href: organizations.index().url,
    },
    {
        title: props.organization.name,
        href: organizations.show({ organization: props.organization.uuid }).url,
    },
    {
        title: 'Add Product',
        href: `/admin/organizations/${props.organization.uuid}/products/create`,
    },
];

const submit = (form: any) => {
    form.post(`/admin/organizations/${props.organization.uuid}/products`);
};
</script>

<template>

    <Head title="Add Product" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4" style="max-width: 800px;">
            <div class="mb-4">
                <h2 class="h4 mb-1">Add Product</h2>
                <p class="text-muted small">Create a new product for {{ organization.name }}.</p>
            </div>

            <div class="card shadow-sm border-0 p-4">
                <ProductForm submit-label="Add Product" :on-submit="submit" />
            </div>
        </div>
    </AppLayout>
</template>
