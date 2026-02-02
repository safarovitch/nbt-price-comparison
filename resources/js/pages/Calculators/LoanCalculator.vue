<script setup lang="ts">
import { ref, computed } from 'vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();

const amount = ref(10000);
const rate = ref(24);
const months = ref(12);

const monthlyPayment = computed(() => {
    const P = amount.value;
    const r = rate.value / 100 / 12;
    const n = months.value;
    
    if (r === 0) return P / n;
    
    return (P * r * Math.pow(1 + r, n)) / (Math.pow(1 + r, n) - 1);
});

const totalPayment = computed(() => {
    return monthlyPayment.value * months.value;
});

const totalInterest = computed(() => {
    return totalPayment.value - amount.value;
});

const activeTab = ref('table'); // 'table' or 'chart' (chart later maybe)

const schedule = computed(() => {
    const schedule = [];
    let balance = amount.value;
    const r = rate.value / 100 / 12;
    const payment = monthlyPayment.value;
    
    for (let i = 1; i <= months.value; i++) {
        const interest = balance * r;
        const principal = payment - interest;
        balance -= principal;
        
        if (balance < 0) balance = 0;
        
        schedule.push({
            month: i,
            payment: payment,
            principal: principal,
            interest: interest,
            balance: balance
        });
    }
    
    return schedule;
});

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'TJS',
        minimumFractionDigits: 2
    }).format(value);
};
</script>

<template>
    <Head :title="__('Loan Calculator')" />
    
    <GuestLayout>
        <div class="bg-light-gray min-vh-100 py-5">
            <div class="container">
                <div class="mb-4">
                    <Link href="/" class="text-decoration-none text-muted mb-2 d-inline-block">
                        <i class="fa-solid fa-arrow-left me-2"></i> {{ __('Back to Home') }}
                    </Link>
                    <h1 class="fw-bold">{{ __('Loan Calculator') }}</h1>
                    <p class="text-muted">{{ __('Calculate your monthly payments and total interest') }}</p>
                </div>

                <div class="row g-4">
                    <!-- Calculator Inputs -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                            <h3 class="fs-5 fw-bold mb-4">{{ __('Parameters') }}</h3>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">{{ __('Loan Amount') }}</label>
                                <div class="input-group mb-2">
                                    <input type="number" v-model="amount" class="form-control form-control-lg fw-bold" min="100">
                                    <span class="input-group-text">TJS</span>
                                </div>
                                <input type="range" v-model="amount" class="form-range" min="100" max="100000" step="100">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">{{ __('Interest Rate') }} (%)</label>
                                <div class="input-group mb-2">
                                    <input type="number" v-model="rate" class="form-control form-control-lg fw-bold" min="1" max="100" step="0.1">
                                    <span class="input-group-text">%</span>
                                </div>
                                <input type="range" v-model="rate" class="form-range" min="1" max="100" step="0.5">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">{{ __('Term') }} ({{ __('months') }})</label>
                                <div class="input-group mb-2">
                                    <input type="number" v-model="months" class="form-control form-control-lg fw-bold" min="1" max="120">
                                    <span class="input-group-text">{{ __('months') }}</span>
                                </div>
                                <input type="range" v-model="months" class="form-range" min="1" max="60" step="1">
                            </div>
                        </div>
                    </div>

                    <!-- Calculator Results -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                            <div class="row text-center g-4">
                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded-4 h-100">
                                        <p class="text-muted small mb-1">{{ __('Monthly Payment') }}</p>
                                        <h3 class="fw-bold text-primary mb-0">{{ formatCurrency(monthlyPayment) }}</h3>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded-4 h-100">
                                        <p class="text-muted small mb-1">{{ __('Total Interest') }}</p>
                                        <h3 class="fw-bold text-danger mb-0">{{ formatCurrency(totalInterest) }}</h3>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded-4 h-100">
                                        <p class="text-muted small mb-1">{{ __('Total Payment') }}</p>
                                        <h3 class="fw-bold text-dark mb-0">{{ formatCurrency(totalPayment) }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Schedule -->
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                                <h3 class="fs-5 fw-bold mb-0">{{ __('Payment Schedule') }}</h3>
                            </div>
                            <div class="table-responsive" style="max-height: 500px">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th class="py-3 ps-4">#</th>
                                            <th class="py-3">{{ __('Payment') }}</th>
                                            <th class="py-3">{{ __('Principal') }}</th>
                                            <th class="py-3">{{ __('Interest') }}</th>
                                            <th class="py-3 pe-4 text-end">{{ __('Balance') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="row in schedule" :key="row.month">
                                            <td class="ps-4">{{ row.month }}</td>
                                            <td class="fw-bold">{{ formatCurrency(row.payment) }}</td>
                                            <td>{{ formatCurrency(row.principal) }}</td>
                                            <td class="text-danger">{{ formatCurrency(row.interest) }}</td>
                                            <td class="pe-4 text-end text-muted">{{ formatCurrency(row.balance) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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
