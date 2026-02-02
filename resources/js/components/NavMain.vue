<script setup lang="ts">
import { urlIsActive } from '@/lib/utils';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

defineProps<{
    items: NavItem[];
}>();

const page = usePage();
</script>

<template>
    <div class="mb-3">
        <h6 class="text-white-50 text-uppercase small mb-2 px-2">Platform</h6>
        <nav class="nav flex-column">
            <Link v-for="item in items" :key="item.title" :href="item.href" :class="[
                'nav-link text-white',
                { 'active bg-primary': urlIsActive(item.href, page.url) },
            ]">
                <component v-if="item.icon" :is="item.icon" class="me-2" style="width: 1rem; height: 1rem;" />
                {{ item.title }}
            </Link>
        </nav>
    </div>
</template>
