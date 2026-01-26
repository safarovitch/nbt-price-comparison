<script setup lang="ts">
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();

defineProps<{
    partners: any[];
}>();
</script>

<template>
    <div class="py-5 bg-light-gray border-top border-bottom overflow-hidden">
        <div class="container py-lg-5">
            <div class="section-title text-center mb-5 animate__animated animate__fadeIn">
                <h2 class="fw-bold fs-1 mb-3 text-dark">{{ __('Our Insurance Partners') }}</h2>
                <p class="text-muted fs-5 mx-auto opacity-75" style="max-width: 700px;">
                    {{ __('We only work with reliable insurance companies with high reliability ratings and proven track record.') }}
                </p>
            </div>

            <div class="row g-4 align-items-stretch justify-content-center">
                <div v-for="merchant in partners" :key="merchant.uuid" class="col-6 col-md-4 col-lg-2 animate__animated animate__fadeInUp">
                    <div class="partner-card bg-white p-4 rounded-4 shadow-sm border border-light text-center transition-all h-100 d-flex flex-column align-items-center justify-content-center">
                        <div class="partner-logo-container mb-3 d-flex align-items-center justify-content-center" style="height: 60px;">
                            <img v-if="merchant.logo" :src="merchant.logo" :alt="merchant.name" class="img-fluid grayscale-hover transition-all" style="max-height: 50px;">
                            <div v-else class="rounded-circle bg-light p-3 border">
                                <i class="fa-solid fa-building-shield fs-3 text-muted opacity-50"></i>
                            </div>
                        </div>
                        <h4 class="fs-6 fw-bold text-dark mb-1">{{ merchant.name }}</h4>
                        <div class="partner-rating d-flex gap-1 justify-content-center">
                            <i v-for="i in 5" :key="i" class="fa-solid fa-star text-warning small" style="font-size: 8px;"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-5">
                <a href="#" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-bold">
                    {{ __('View All Ratings') }}
                </a>
            </div>
        </div>
    </div>
</template>

<style scoped>
.bg-light-gray {
    background-color: #f9fbff;
}

.partner-card {
    cursor: pointer;
}

.partner-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 30px -10px rgba(var(--bs-primary-rgb), 0.15) !important;
    border-color: rgba(var(--bs-primary-rgb), 0.2) !important;
}

.grayscale-hover {
    filter: grayscale(1);
    opacity: 0.6;
}

.partner-card:hover .grayscale-hover {
    filter: grayscale(0);
    opacity: 1;
}

.transition-all {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.partner-logo-container {
    width: 100%;
}
</style>
