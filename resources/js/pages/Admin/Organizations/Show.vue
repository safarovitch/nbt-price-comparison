<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import organizations from '@/routes/admin/organizations';
import { type BreadcrumbItem, type Organization, type Product } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Building2, Edit, ExternalLink, Package, Plus, RefreshCw, Trash2, AlertCircle } from 'lucide-vue-next';
import { formatDistanceToNow } from 'date-fns';
import { ref, watch, onUnmounted, computed } from 'vue';
import { getTranslation } from '@/utils/translations';

const props = defineProps<{
    organization: Organization & { products: Product[] };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Organizations',
        href: organizations.index().url,
    },
    {
        title: getTranslation(props.organization.name),
        href: organizations.show({ organization: props.organization.uuid }).url,
    },
];

// Track local syncing state for immediate UI feedback
const localSyncing = ref(false);

// Check if syncing (either from server or local tracking)
const isSyncing = computed(() =>
    props.organization.last_sync_status === 'syncing' || localSyncing.value
);

let pollInterval: ReturnType<typeof setInterval> | null = null;
let pollCount = ref(0);

const startPolling = () => {
    if (pollInterval) return;
    pollCount.value = 0;

    pollInterval = setInterval(() => {
        pollCount.value++;

        // Safety: stop polling after 60 attempts (2 minutes)
        if (pollCount.value > 60) {
            localSyncing.value = false;
            stopPolling();
            return;
        }

        router.reload({
            preserveScroll: true,
        });
    }, 2000);
};

const stopPolling = () => {
    if (pollInterval) {
        clearInterval(pollInterval);
        pollInterval = null;
    }
    pollCount.value = 0;
};

// Watch for server status changes to stop polling
watch(() => props.organization.last_sync_status, (newStatus) => {
    // Job finished (success or failed) - clear local state and stop polling
    if (newStatus === 'success' || newStatus === 'failed' || newStatus === 'idle') {
        localSyncing.value = false;
        stopPolling();
    }
}, { immediate: true });

// Cleanup on unmount
onUnmounted(() => {
    stopPolling();
});

const deleteProduct = (product: Product) => {
    if (confirm('Are you sure you want to delete this product?')) {
        router.delete(`/admin/organizations/${props.organization.uuid}/products/${product.uuid}`);
    }
};

const triggerSync = () => {
    // Immediately mark as syncing locally for instant UI feedback
    localSyncing.value = true;

    router.post(`/admin/organizations/${props.organization.uuid}/sync`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            startPolling();
        },
        onError: () => {
            localSyncing.value = false;
        }
    });
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

const displayStatus = computed(() => {
    if (localSyncing.value) return 'syncing';
    return props.organization.last_sync_status || 'idle';
});
</script>

<template>

    <Head :title="organization.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <!-- Header -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div class="d-flex align-items-center gap-3">
                    <Link :href="organizations.index().url" class="btn btn-outline-secondary btn-sm p-2 rounded-circle">
                        <ArrowLeft style="width: 1.25rem; height: 1.25rem;" />
                    </Link>
                    <div>
                        <h2 class="h4 mb-1 d-flex align-items-center gap-2">
                            <Building2 class="text-muted" />
                            {{ getTranslation(organization.name) }}
                        </h2>
                        <div class="d-flex align-items-center gap-2">
                            <span :class="['badge', organization.status === 'active' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning']">
                                {{ organization.status }}
                            </span>
                            <span class="text-muted small">| {{ organization.type }}</span>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button @click="triggerSync" class="btn btn-outline-primary d-inline-flex align-items-center gap-2" :disabled="isSyncing">
                        <RefreshCw style="width: 1rem; height: 1rem;" :class="{ 'spin-animation': isSyncing }" />
                        {{ isSyncing ? 'Syncing...' : 'Sync Products' }}
                    </button>
                    <Link :href="organizations.edit({ organization: organization.uuid }).url" class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                        <Edit style="width: 1rem; height: 1rem;" />
                        Edit
                    </Link>
                </div>
            </div>

            <!-- Sync Error Alert -->
            <div v-if="organization.last_sync_status === 'failed' && organization.last_sync_error" class="alert alert-danger d-flex align-items-start gap-2 mb-4">
                <AlertCircle style="width: 1.25rem; height: 1.25rem; flex-shrink: 0; margin-top: 2px;" />
                <div>
                    <strong>Last sync failed:</strong>
                    <pre class="mb-0 mt-1 small" style="white-space: pre-wrap;">{{ organization.last_sync_error }}</pre>
                </div>
            </div>

            <div class="row g-4">
                <!-- Info Column -->
                <div class="col-12 col-lg-4">
                    <div class="card shadow-sm border-0 p-4 h-100">
                        <h5 class="fw-bold mb-3">Organization Details</h5>
                        <div class="mb-3">
                            <label class="text-muted small text-uppercase fw-bold d-block">Website</label>
                            <a :href="organization.website" target="_blank" class="text-primary text-decoration-none d-flex align-items-center gap-1">
                                {{ organization.website || 'N/A' }}
                                <ExternalLink v-if="organization.website" style="width: 0.875rem; height: 0.875rem;" />
                            </a>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small text-uppercase fw-bold d-block">Base URL</label>
                            <code class="small">{{ organization.base_url || 'N/A' }}</code>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small text-uppercase fw-bold d-block">Registration #</label>
                            <span>{{ organization.registration_number || 'N/A' }}</span>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small text-uppercase fw-bold d-block">TIN</label>
                            <span>{{ organization.tin || 'N/A' }}</span>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small text-uppercase fw-bold d-block">Sync Status</label>
                            <span :class="['badge', getSyncStatusBadgeClass(displayStatus)]">
                                {{ displayStatus }}
                            </span>
                        </div>
                        <div class="mb-0">
                            <label class="text-muted small text-uppercase fw-bold d-block">Last Sync</label>
                            <span class="small text-muted">{{ formatLastSynced(organization.last_synced_at) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Products Column -->
                <div class="col-12 col-lg-8">
                    <div class="card shadow-sm border-0 overflow-hidden">
                        <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                                <Package class="text-muted" />
                                Products ({{ organization.products.length }})
                            </h5>
                            <Link :href="`/admin/organizations/${organization.uuid}/products/create`" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-2">
                                <Plus style="width: 1rem; height: 1rem;" />
                                Add Product
                            </Link>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th class="px-4 py-3">Product Name</th>
                                        <th class="py-3">Type</th>
                                        <th class="py-3">Interest Rate</th>
                                        <th class="px-4 py-3 text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="product in organization.products" :key="product.uuid">
                                        <td class="px-4">
                                            <div class="fw-bold">{{ getTranslation(product.name) }}</div>

                                            <div class="small text-muted">{{ product.currency_code }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ product.type }}</span>
                                        </td>
                                        <td>
                                            {{ product.interest_rate ? `${product.interest_rate}%` : 'N/A' }}
                                        </td>
                                        <td class="px-4 text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <Link :href="`/admin/organizations/${organization.uuid}/products/${product.uuid}/edit`" class="btn btn-sm btn-outline-primary p-1">
                                                    <Edit style="width: 0.875rem; height: 0.875rem;" />
                                                </Link>
                                                <button @click="deleteProduct(product)" class="btn btn-sm btn-outline-danger p-1">
                                                    <Trash2 style="width: 0.875rem; height: 0.875rem;" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="organization.products.length === 0">
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            No products synchronized yet.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
@keyframes spin {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}

.spin-animation {
    animation: spin 1s linear infinite;
}
</style>
