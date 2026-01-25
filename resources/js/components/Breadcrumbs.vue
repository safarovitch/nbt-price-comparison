<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface BreadcrumbItemType {
    title: string;
    href?: string;
}

defineProps<{
    breadcrumbs: BreadcrumbItemType[];
}>();
</script>

<template>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <template v-for="(item, index) in breadcrumbs" :key="index">
                <li
                    v-if="index === breadcrumbs.length - 1"
                    class="breadcrumb-item active"
                    aria-current="page"
                >
                    {{ item.title }}
                </li>
                <li v-else class="breadcrumb-item">
                    <Link :href="item.href ?? '#'">{{ item.title }}</Link>
                </li>
            </template>
        </ol>
    </nav>
</template>
