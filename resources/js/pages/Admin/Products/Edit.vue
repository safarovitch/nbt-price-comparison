<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import organizations from '@/routes/admin/organizations';
import { type BreadcrumbItem, type Organization, type Product } from '@/types';
import { Head } from '@inertiajs/vue3';
import ProductForm from './Partials/ProductForm.vue';

const props = defineProps<{
    organization: Organization;
    product: Product;
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
        title: props.product.name,
        href: `/admin/organizations/${props.organization.uuid}/products/${props.product.uuid}/edit`,
    },
];

const submit = (form: any) => {
    form.put(`/admin/organizations/${props.organization.uuid}/products/${props.product.uuid}`);
};
</script>

<template>

    <Head :title="`Edit ${product.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4" style="max-width: 800px;">
            <div class="mb-4">
                <h2 class="h4 mb-1">Edit Product</h2>
                <p class="text-muted small">Update product details for {{ organization.name }}.</p>
            </div>

            <div class="card shadow-sm border-0 p-4">
                <ProductForm :product="product" submit-label="Save Changes" :on-submit="submit" />
            </div>
        </div>
    </AppLayout>
</template>
