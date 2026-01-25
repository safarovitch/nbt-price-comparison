<script setup lang="ts">
interface Props {
    variant?: 'header' | 'sidebar';
}

defineProps<Props>();
</script>

<template>
    <div class="d-flex min-vh-100 w-100 flex-column">
        <slot />
    </div>
</template>
