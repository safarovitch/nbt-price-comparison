<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <img src="/storage/images/nbt/logo_nbt_en.png" alt="NBT Logo" :class="className" v-bind="$attrs" style="height: 100%; max-height: 60px; width: auto; object-fit: contain;">
</template>
