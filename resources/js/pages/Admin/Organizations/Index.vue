<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import organizationsRoutes from '@/routes/admin/organizations';
import { type BreadcrumbItem, type Organization } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Building2, Edit, Eye, Plus, Trash2 } from 'lucide-vue-next';
import { formatDistanceToNow } from 'date-fns';
import { getTranslation } from '@/utils/translations';

interface Props {
    organizations: {
        data: Organization[];
        links: any[];
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Organizations',
        href: organizationsRoutes.index().url,
    },
];

const deleteOrganization = (organization: Organization) => {
    if (confirm('Are you sure you want to delete this organization? All associated products will also be deleted.')) {
        router.delete(organizationsRoutes.destroy({ organization: organization.uuid }).url);
    }
};

const formatLastSynced = (date: string | null) => {
    if (!date) return 'Never';
    return formatDistanceToNow(new Date(date), { addSuffix: true });
};

const getSyncStatusBadgeClass = (status: string) => {
    switch (status) {
        case 'syncing': return 'bg-info-subtle text-info';
        case 'success': return 'bg-success-subtle text-success';
        case 'failed': return 'bg-danger-subtle text-danger';
        default: return 'bg-secondary-subtle text-secondary';
    }
};
</script>

<template>

    <Head title="Organizations" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 mb-0 d-flex align-items-center gap-2">
                    <Building2 class="text-muted" />
                    Organizations
                </h2>
                <Link :href="organizationsRoutes.create().url" class="btn btn-primary d-inline-flex align-items-center gap-2">
                    <Plus style="width: 1.25rem; height: 1.25rem;" />
                    Add Organization
                </Link>
            </div>

            <div class="card shadow-sm border-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="px-4 py-3">Name</th>
                                <th class="py-3">Type</th>
                                <th class="py-3">Status</th>
                                <th class="py-3">Sync Status</th>
                                <th class="py-3">Last Synced</th>
                                <th class="px-4 py-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="org in organizations.data" :key="org.uuid">
                                <td class="px-4">
                                    <div class="fw-bold text-dark">{{ getTranslation(org.name) }}</div>

                                    <div class="small text-muted">{{ org.website }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ org.type }}</span>
                                </td>
                                <td>
                                    <span :class="['badge', org.status === 'active' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning']">
                                        {{ org.status }}
                                    </span>
                                </td>
                                <td>
                                    <span :class="['badge', getSyncStatusBadgeClass(org.last_sync_status || 'idle')]">
                                        {{ org.last_sync_status || 'idle' }}
                                    </span>
                                </td>
                                <td class="small text-muted">
                                    {{ formatLastSynced(org.last_synced_at) }}
                                </td>
                                <td class="px-4 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <Link :href="organizationsRoutes.show({ organization: org.uuid }).url" class="btn btn-sm btn-outline-secondary p-1" title="View">
                                            <Eye style="width: 1rem; height: 1rem;" />
                                        </Link>
                                        <Link :href="organizationsRoutes.edit({ organization: org.uuid }).url" class="btn btn-sm btn-outline-primary p-1" title="Edit">
                                            <Edit style="width: 1rem; height: 1rem;" />
                                        </Link>
                                        <button @click="deleteOrganization(org)" class="btn btn-sm btn-outline-danger p-1" title="Delete">
                                            <Trash2 style="width: 1rem; height: 1rem;" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!organizations?.data || organizations.data.length === 0">
                                <td colspan="6" class="text-center py-5 text-muted">
                                    No organizations found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="organizations?.links && organizations.links.length > 3" class="mt-4 d-flex justify-content-center">
                <nav>
                    <ul class="pagination pagination-sm">
                        <li v-for="(link, k) in organizations.links" :key="k" class="page-item" :class="{ 'active': link.active, 'disabled': !link.url }">
                            <Link :href="link.url || '#'" class="page-link" v-html="link.label" />
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </AppLayout>
</template>
