<script setup lang="ts">
import { toUrl } from '@/lib/utils';
import { type NavItem } from '@/types';

interface Props {
    items: NavItem[];
    class?: string;
}

defineProps<Props>();
</script>

<template>
    <nav class="nav flex-column">
        <a v-for="item in items" :key="item.title" :href="toUrl(item.href)" target="_blank" rel="noopener noreferrer" class="nav-link text-white-50">
            <component v-if="item.icon" :is="item.icon" class="me-2" style="width: 1rem; height: 1rem;" />
            {{ item.title }}
        </a>
    </nav>
</template>
