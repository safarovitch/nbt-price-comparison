<script setup lang="ts">
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();

defineProps<{
    title: string;
    description: string;
    icon: string;
    colorClass?: string;
}>();

const emit = defineEmits<{
    (e: 'click'): void;
}>();
</script>

<template>
    <div @click="emit('click')" class="category-card p-4 rounded-4 h-100 position-relative transition-all cursor-pointer bg-white border border-light">
        <div class="d-flex align-items-start justify-content-between mb-3">
            <div class="icon-box rounded-3 d-flex align-items-center justify-content-center" :class="colorClass || 'bg-primary-soft text-primary'">
                <i :class="icon" class="fs-3"></i>
            </div>
            <div class="action-icon opacity-0 transition-all">
                <i class="fa-solid fa-chevron-right text-muted"></i>
            </div>
        </div>

        <h3 class="fs-5 fw-bold mb-2 text-dark">{{ title }}</h3>
        <p class="text-muted small mb-0 lh-base opacity-75">{{ description }}</p>
    </div>
</template>

<style scoped>
.category-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.01);
    border-color: rgba(var(--bs-primary-rgb), 0.2) !important;
}

.category-card:hover .action-icon {
    opacity: 1;
    transform: translateX(5px);
}

.icon-box {
    width: 56px;
    height: 56px;
}

.bg-primary-soft {
    background-color: rgba(var(--bs-primary-rgb), 0.1);
}

.text-primary {
    color: var(--bs-primary) !important;
}

.bg-success-soft {
    background-color: rgba(var(--bs-success-rgb), 0.1);
}

.text-success {
    color: var(--bs-success) !important;
}

.bg-info-soft {
    background-color: rgba(var(--bs-info-rgb), 0.1);
}

.text-info {
    color: var(--bs-info) !important;
}

.bg-warning-soft {
    background-color: rgba(var(--bs-warning-rgb), 0.1);
}

.text-warning {
    color: var(--bs-warning) !important;
}

.cursor-pointer {
    cursor: pointer;
}

.rounded-4 {
    border-radius: 1.25rem !important;
}

.rounded-3 {
    border-radius: 1rem !important;
}
</style>
