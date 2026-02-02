<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import organizations from '@/routes/admin/organizations';
import { type BreadcrumbItem, type Organization } from '@/types';
import { Head } from '@inertiajs/vue3';
import OrganizationForm from './Partials/OrganizationForm.vue';
import { getTranslation } from '@/utils/translations';

const props = defineProps<{
    organization: Organization;
}>();

const organizationName = getTranslation(props.organization.name);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Organizations',
        href: organizations.index().url,
    },
    {
        title: organizationName,
        href: organizations.show({ organization: props.organization.uuid }).url,
    },
    {
        title: 'Edit',
        href: organizations.edit({ organization: props.organization.uuid }).url,
    },
];

const submit = (form: any) => {
    form.put(organizations.update({ organization: props.organization.uuid }).url);
};
</script>

<template>

    <Head :title="`Edit ${organizationName}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4" style="max-width: 800px;">
            <div class="mb-4">
                <h2 class="h4 mb-1">Edit Organization</h2>
                <p class="text-muted small">Update organization details and API credentials.</p>
            </div>

            <div class="card shadow-sm border-0 p-4">
                <OrganizationForm :organization="organization" submit-label="Save Changes" :on-submit="submit" />
            </div>
        </div>
    </AppLayout>
</template>
