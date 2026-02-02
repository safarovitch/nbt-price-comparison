<script setup lang="ts">
import { ref, computed } from 'vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();

const balance = ref(5000);
const rate = ref(30); // Higher rate for credit cards
const monthlyPayment = ref(500);

// Warnings
const warning = computed(() => {
    const r = rate.value / 100 / 12;
    const interest = balance.value * r;
    if (monthlyPayment.value <= interest) {
        return "Monthly payment is too low to cover interest. You will never pay off this debt.";
    }
    return null;
});

const results = computed(() => {
    if (warning.value) return null;

    let b = balance.value;
    const r = rate.value / 100 / 12;
    let months = 0;
    let totalInterest = 0;
    let totalPayment = 0;

    // Limit to 30 years (360 months) to avoid infinite loops if close to interest
    const maxMonths = 360; 

    while (b > 0 && months < maxMonths) {
        let interest = b * r;
        let principal = monthlyPayment.value - interest;
        
        if (b < principal) {
            // Final payment
            principal = b;
            interest = b * r; // approx
            totalPayment += (principal + interest);
        } else {
            totalPayment += monthlyPayment.value;
        }

        b -= principal;
        totalInterest += interest;
        months++;
    }

    return {
        months,
        totalInterest,
        totalPayment
    };
});

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'TJS',
        minimumFractionDigits: 2
    }).format(value);
};

const formatMonths = (m: number) => {
    const years = Math.floor(m / 12);
    const months = m % 12;
    if (years > 0) {
        return `${years} ${__('years')} ${months} ${__('months')}`;
    }
    return `${months} ${__('months')}`;
};
</script>

<template>
    <Head :title="__('Credit Calculator')" />
    
    <GuestLayout>
        <div class="bg-light-gray min-vh-100 py-5">
            <div class="container">
                <div class="mb-4">
                    <Link href="/" class="text-decoration-none text-muted mb-2 d-inline-block">
                        <i class="fa-solid fa-arrow-left me-2"></i> {{ __('Back to Home') }}
                    </Link>
                    <h1 class="fw-bold">{{ __('Credit Calculator') }}</h1>
                    <p class="text-muted">{{ __('Calculate how long it will take to pay off your credit card') }}</p>
                </div>

                <div class="row g-4">
                    <!-- Calculator Inputs -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                            <h3 class="fs-5 fw-bold mb-4">{{ __('Parameters') }}</h3>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">{{ __('Credit Balance') }}</label>
                                <div class="input-group mb-2">
                                    <input type="number" v-model="balance" class="form-control form-control-lg fw-bold" min="100">
                                    <span class="input-group-text">TJS</span>
                                </div>
                                <input type="range" v-model="balance" class="form-range" min="100" max="50000" step="100">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">{{ __('Interest Rate') }} (%)</label>
                                <div class="input-group mb-2">
                                    <input type="number" v-model="rate" class="form-control form-control-lg fw-bold" min="1" max="100" step="0.1">
                                    <span class="input-group-text">%</span>
                                </div>
                                <input type="range" v-model="rate" class="form-range" min="15" max="60" step="0.5">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">{{ __('Monthly Payment') }}</label>
                                <div class="input-group mb-2">
                                    <input type="number" v-model="monthlyPayment" class="form-control form-control-lg fw-bold" min="10">
                                    <span class="input-group-text">TJS</span>
                                </div>
                                <input type="range" v-model="monthlyPayment" class="form-range" min="50" max="5000" step="10">
                            </div>
                        </div>
                    </div>

                    <!-- Calculator Results -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                            <div v-if="warning" class="alert alert-danger mb-0">
                                {{ __(warning) }}
                            </div>
                            <div v-else class="row text-center g-4">
                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded-4 h-100">
                                        <p class="text-muted small mb-1">{{ __('Time to Pay Off') }}</p>
                                        <h3 class="fw-bold text-primary mb-0">{{ formatMonths(results?.months || 0) }}</h3>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded-4 h-100">
                                        <p class="text-muted small mb-1">{{ __('Total Interest') }}</p>
                                        <h3 class="fw-bold text-danger mb-0">{{ formatCurrency(results?.totalInterest || 0) }}</h3>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded-4 h-100">
                                        <p class="text-muted small mb-1">{{ __('Total Payment') }}</p>
                                        <h3 class="fw-bold text-dark mb-0">{{ formatCurrency(results?.totalPayment || 0) }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Info Card -->
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-primary text-white">
                            <h3 class="fs-4 fw-bold mb-3">{{ __('Did you know?') }}</h3>
                            <p class="mb-0 opacity-75">
                                {{ __('Paying just a little more than your minimum payment can significantly reduce the amount of interest you pay and help you get out of debt much faster.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
.form-control-lg {
    font-size: 1.25rem;
}
</style>
