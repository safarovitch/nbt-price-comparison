<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
}>();
</script>

<template>
    <nav v-if="links.length > 3">
        <ul class="pagination justify-content-center">
            <li v-for="(link, key) in links" :key="key" class="page-item" :class="{ 'active': link.active, 'disabled': !link.url }">
                <Link v-if="link.url" :href="link.url" class="page-link" v-html="link.label" />
                <span v-else class="page-link" v-html="link.label"></span>
            </li>
        </ul>
    </nav>
</template>

<style scoped>
.page-link {
    cursor: pointer;
}
</style>
