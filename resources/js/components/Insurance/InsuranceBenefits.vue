<script setup lang="ts">
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();

const benefits = [
    {
        title: __('Official Prices'),
        desc: __('Direct prices from insurance companies without hidden commissions and extra charges.'),
        icon: 'fa-solid fa-file-shield'
    },
    {
        title: __('Help with Choice'),
        desc: __('We show insurance ratings, cost details, and no-claim discounts to make it easy.'),
        icon: 'fa-solid fa-hand-holding-heart'
    },
    {
        title: __('Save All Discounts'),
        desc: __('All your existing discounts are applied automatically via unified database check.'),
        icon: 'fa-solid fa-award'
    }
];
</script>

<template>
    <div class="py-5 bg-white position-relative">
        <div class="container py-lg-5">
            <div class="row g-4 justify-content-center">
                <div v-for="benefit in benefits" :key="benefit.title" class="col-lg-4">
                    <div class="benefit-card h-100 p-4 p-xl-5 border-0 rounded-5 transition-all hover-shadow-lg">
                        <div class="icon-wrapper mb-4 d-flex align-items-center justify-content-center shadow-sm">
                            <i :class="benefit.icon" class="fs-1 text-primary"></i>
                        </div>
                        <h3 class="fs-4 fw-bold mb-3 text-dark">{{ benefit.title }}</h3>
                        <p class="text-muted fs-6 lh-base mb-0 opacity-75">{{ benefit.desc }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.benefit-card {
    background: #f8fbff;
    border: 1px solid rgba(0, 0, 0, 0.03) !important;
}

.benefit-card:hover {
    background: #ffffff;
    transform: translateY(-8px);
    border-color: rgba(var(--bs-primary-rgb), 0.1) !important;
}

.icon-wrapper {
    width: 80px;
    height: 80px;
    background: #ffffff;
    border-radius: 24px;
    transition: all 0.3s ease;
}

.benefit-card:hover .icon-wrapper {
    background: var(--bs-primary);
}

.benefit-card:hover .icon-wrapper i {
    color: #ffffff !important;
}

.hover-shadow-lg:hover {
    box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.08);
}

.rounded-5 {
    border-radius: 2rem !important;
}
</style>
