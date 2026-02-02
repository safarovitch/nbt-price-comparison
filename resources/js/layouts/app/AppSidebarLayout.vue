<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <!-- Add margin-left on desktop to offset fixed sidebar (16rem width) -->
        <div class="d-flex flex-column flex-fill" style="margin-left: 0;">
            <div class="d-none d-md-block" style="margin-left: 16rem;">
                <AppContent variant="sidebar" class="overflow-x-hidden">
                    <AppSidebarHeader :breadcrumbs="breadcrumbs" />
                    <slot />
                </AppContent>
            </div>
            <!-- Mobile: no margin -->
            <div class="d-md-none">
                <AppContent variant="sidebar" class="overflow-x-hidden">
                    <AppSidebarHeader :breadcrumbs="breadcrumbs" />
                    <slot />
                </AppContent>
            </div>
        </div>
    </AppShell>
</template>
