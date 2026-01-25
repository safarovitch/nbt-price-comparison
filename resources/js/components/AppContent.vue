<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    variant?: 'header' | 'sidebar';
    class?: string;
}

const props = defineProps<Props>();
const className = computed(() => props.class);
</script>

<template>
    <main
        class="mx-auto d-flex h-100 w-100 flex-fill flex-column gap-3 rounded"
        :class="className"
        style="max-width: 80rem;"
    >
        <slot />
    </main>
</template>
