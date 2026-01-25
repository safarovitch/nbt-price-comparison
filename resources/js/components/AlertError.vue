<script setup lang="ts">
import { AlertCircle } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    errors: string[];
    title?: string;
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Something went wrong.',
});

const uniqueErrors = computed(() => Array.from(new Set(props.errors)));
</script>

<template>
    <div class="alert alert-danger d-flex align-items-start" role="alert">
        <AlertCircle class="me-2 flex-shrink-0" style="width: 1rem; height: 1rem; margin-top: 0.125rem;" />
        <div class="flex-grow-1">
            <h6 class="alert-heading mb-2">{{ title }}</h6>
            <ul class="mb-0 ps-3 small">
                <li v-for="(error, index) in uniqueErrors" :key="index">
                    {{ error }}
                </li>
            </ul>
        </div>
    </div>
</template>
