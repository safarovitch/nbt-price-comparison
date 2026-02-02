<script setup lang="ts">
import { ref, computed } from 'vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();

const amount = ref(5000);
const rate = ref(14);
const months = ref(12);
const capitalization = ref(true); // Monthly capitalization

const results = computed(() => {
    const P = amount.value;
    const r = rate.value / 100 / 12;
    const n = months.value;
    
    let totalAmount = 0;
    let profit = 0;

    if (capitalization.value) {
        // Compound Interest: A = P(1 + r)^n
        totalAmount = P * Math.pow(1 + r, n);
    } else {
        // Simple Interest: A = P(1 + r*n)
        totalAmount = P * (1 + r * n);
    }

    profit = totalAmount - P;

    return {
        totalAmount,
        profit
    };
});

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'TJS',
        minimumFractionDigits: 2
    }).format(value);
};

// Schedule for visualization if needed
const schedule = computed(() => {
    const data = [];
    let balance = amount.value;
    const r = rate.value / 100 / 12;
    
    for (let i = 1; i <= months.value; i++) {
        let interest = 0;
        if (capitalization.value) {
            interest = balance * r;
            balance += interest;
        } else {
            interest = amount.value * r;
            balance += interest; // Accumulating total value including payout
        }
        
        data.push({
            month: i,
            balance: balance,
            interest: interest,
            totalInterest: balance - amount.value
        });
    }
    return data;
});

</script>

<template>
    <Head :title="__('Deposit Calculator')" />
    
    <GuestLayout>
        <div class="bg-light-gray min-vh-100 py-5">
            <div class="container">
                <div class="mb-4">
                    <Link href="/" class="text-decoration-none text-muted mb-2 d-inline-block">
                        <i class="fa-solid fa-arrow-left me-2"></i> {{ __('Back to Home') }}
                    </Link>
                    <h1 class="fw-bold">{{ __('Deposit Calculator') }}</h1>
                    <p class="text-muted">{{ __('Calculate your potential savings and profit') }}</p>
                </div>

                <div class="row g-4">
                    <!-- Calculator Inputs -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                            <h3 class="fs-5 fw-bold mb-4">{{ __('Parameters') }}</h3>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">{{ __('Deposit Amount') }}</label>
                                <div class="input-group mb-2">
                                    <input type="number" v-model="amount" class="form-control form-control-lg fw-bold" min="100">
                                    <span class="input-group-text">TJS</span>
                                </div>
                                <input type="range" v-model="amount" class="form-range" min="100" max="100000" step="100">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">{{ __('Interest Rate') }} (%)</label>
                                <div class="input-group mb-2">
                                    <input type="number" v-model="rate" class="form-control form-control-lg fw-bold" min="1" max="50" step="0.1">
                                    <span class="input-group-text">%</span>
                                </div>
                                <input type="range" v-model="rate" class="form-range" min="1" max="30" step="0.5">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">{{ __('Term') }} ({{ __('months') }})</label>
                                <div class="input-group mb-2">
                                    <input type="number" v-model="months" class="form-control form-control-lg fw-bold" min="1" max="60">
                                    <span class="input-group-text">{{ __('months') }}</span>
                                </div>
                                <input type="range" v-model="months" class="form-range" min="1" max="36" step="1">
                            </div>

                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" id="capitalization" v-model="capitalization">
                                <label class="form-check-label fw-bold" for="capitalization">{{ __('Monthly Capitalization') }}</label>
                                <p class="text-muted small mb-0">{{ __('Reinvest interest every month') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Calculator Results -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                            <div class="row text-center g-4">
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded-4 h-100">
                                        <p class="text-muted small mb-1">{{ __('Total Profit') }}</p>
                                        <h3 class="fw-bold text-success mb-0">{{ formatCurrency(results.profit) }}</h3>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded-4 h-100">
                                        <p class="text-muted small mb-1">{{ __('Total Amount at End') }}</p>
                                        <h3 class="fw-bold text-dark mb-0">{{ formatCurrency(results.totalAmount) }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Schedule -->
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-header bg-white border-bottom p-3">
                                <h3 class="fs-5 fw-bold mb-0">{{ __('Growth Schedule') }}</h3>
                            </div>
                            <div class="table-responsive" style="max-height: 500px">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th class="py-3 ps-4">#</th>
                                            <th class="py-3">{{ __('Interest Earned') }}</th>
                                            <th class="py-3">{{ __('Total Interest') }}</th>
                                            <th class="py-3 pe-4 text-end">{{ __('Total Balance') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="row in schedule" :key="row.month">
                                            <td class="ps-4">{{ row.month }}</td>
                                            <td class="text-success">+{{ formatCurrency(row.interest) }}</td>
                                            <td>{{ formatCurrency(row.totalInterest) }}</td>
                                            <td class="pe-4 text-end fw-bold">{{ formatCurrency(row.balance) }}</td>
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
