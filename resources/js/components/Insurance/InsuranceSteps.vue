<script setup lang="ts">
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();

const steps = [
    {
        num: '01',
        title: __('Compare'),
        desc: __('Enter your details and get offers from multiple companies.')
    },
    {
        num: '02',
        title: __('Choose'),
        desc: __('Select the best offer based on price and rating.')
    },
    {
        num: '03',
        title: __('Buy'),
        desc: __('Pay online and receive the policy via email instantly.')
    }
];
</script>

<template>
    <div class="py-5 bg-white">
        <div class="container py-lg-5">
            <h2 class="fw-bold fs-1 text-center mb-5">{{ __('How to buy insurance online') }}</h2>

            <div class="row g-0 justify-content-center position-relative">
                <!-- Step Connector Line (Desktop) -->
                <div class="d-none d-md-block position-absolute top-50 start-50 translate-middle w-75 h-px bg-light z-0 mt-n5"></div>

                <div v-for="(step, index) in steps" :key="step.num" class="col-md-4 mb-5 mb-md-0 position-relative z-1 animate__animated animate__fadeInUp" :style="`animation-delay: ${index * 0.2}s`">
                    <div class="step-card px-4 text-center">
                        <div class="step-number-container mb-4 mx-auto">
                            <div class="step-number bg-white text-primary fw-bold shadow-sm d-flex align-items-center justify-content-center mx-auto">
                                {{ step.num }}
                            </div>
                        </div>
                        <h3 class="fs-4 fw-bold mb-3">{{ step.title }}</h3>
                        <p class="text-muted px-lg-4 lh-base opacity-75">{{ step.desc }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.h-px {
    height: 2px;
}

.mt-n5 {
    margin-top: -3rem;
}

.step-number {
    width: 64px;
    height: 64px;
    border-radius: 20px;
    font-size: 1.5rem;
    border: 2px solid rgba(var(--bs-primary-rgb), 0.1);
    transition: all 0.3s ease;
}

.step-card:hover .step-number {
    background: var(--bs-primary) !important;
    color: white !important;
    transform: rotate(10deg);
}

.step-number-container {
    padding: 10px;
    background: radial-gradient(circle, rgba(var(--bs-primary-rgb), 0.05) 0%, transparent 70%);
}
</style>
