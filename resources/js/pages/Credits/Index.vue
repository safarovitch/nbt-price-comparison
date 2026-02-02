<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import GuestLayout from '@/layouts/GuestLayout.vue';
import CreditCard from '@/components/CreditCard.vue';
import { useTrans } from '@/composables/useTrans';
import { ref, computed, watch } from 'vue';
import { getTranslation } from '@/utils/translations';

const { __ } = useTrans();

interface Organization {
    uuid: string;
    name: Record<string, string>;
}

interface Product {
    uuid: string;
    name: Record<string, string>;
    type: string;
    currency_code: number;
    interest_rate_min: number | null;
    interest_rate_max: number | null;
    term_months_min: number | null;
    term_months_max: number | null;
    min_amount: number | null;
    max_amount: number | null;
    purpose: string | null;
    eligibility: Record<string, string> | null;
    description: Record<string, string> | null;
    organization: Organization;
}

interface Props {
    products: {
        data: Product[];
        links: any;
        meta?: any;
    };
    organizations: Organization[];
    purposes: string[];
    termOptions: number[];
    filters: {
        amount: string | null;
        term: string | null;
        purpose: string | null;
        organization: string | null;
        sort: string;
    };
}

const props = defineProps<Props>();

// Calculator state
const loanAmount = ref(props.filters.amount ? parseInt(props.filters.amount) : 50000);
const loanTerm = ref(props.filters.term ? parseInt(props.filters.term) : 12);

// Filter state
const purpose = ref(props.filters.purpose || '');
const organization = ref(props.filters.organization || '');
const sort = ref(props.filters.sort || 'rate_asc');

// Amount slider config
const minAmount = 1000;
const maxAmount = 500000;
const amountStep = 1000;

// Term slider config  
const minTerm = 3;
const maxTerm = 60;

// Calculate monthly payment (simple approximation)
const monthlyPayment = computed(() => {
    const rate = 0.22 / 12; // Average 22% annual rate
    const n = loanTerm.value;
    const p = loanAmount.value;
    if (rate === 0) return Math.round(p / n);
    const payment = p * (rate * Math.pow(1 + rate, n)) / (Math.pow(1 + rate, n) - 1);
    return Math.round(payment);
});

const sortOptions = [
    { value: 'rate_asc', label: 'По ставке ↑' },
    { value: 'rate_desc', label: 'По ставке ↓' },
    { value: 'amount_desc', label: 'По сумме ↓' },
    { value: 'amount_asc', label: 'По сумме ↑' },
];

const purposeOptions = [
    { value: '', label: 'Любая цель' },
    { value: 'consumer', label: 'Потребительский' },
    { value: 'auto', label: 'Автокредит' },
    { value: 'mortgage', label: 'Ипотека' },
    { value: 'business', label: 'Бизнес' },
    { value: 'education', label: 'Образование' },
];

const formatCurrency = (value: number) => {
    return value.toLocaleString('ru-RU') + ' с.';
};

const applyFilters = () => {
    router.get('/credits', {
        amount: loanAmount.value || undefined,
        term: loanTerm.value || undefined,
        purpose: purpose.value || undefined,
        organization: organization.value || undefined,
        sort: sort.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    loanAmount.value = 50000;
    loanTerm.value = 12;
    purpose.value = '';
    organization.value = '';
    sort.value = 'rate_asc';
    router.get('/credits');
};

// Debounced apply on calculator change
let debounceTimer: ReturnType<typeof setTimeout>;
const debouncedApply = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(applyFilters, 500);
};

watch([loanAmount, loanTerm], debouncedApply);
watch(sort, applyFilters);
</script>

<template>

    <Head title="Кредиты и займы - Сравнение" />

    <GuestLayout>
        <!-- Loan Calculator Hero -->
        <div class="calculator-hero">
            <div class="container">
                <div class="calculator-card">
                    <h1 class="calculator-title">{{ __('Credits and Loans') }}</h1>
                    <p class="calculator-subtitle">{{ __('Compare offers and find the best rate') }}</p>

                    <div class="calculator-grid">
                        <!-- Amount Slider -->
                        <div class="calculator-input">
                            <div class="input-header">
                                <label>{{ __('Loan Amount') }}</label>
                                <span class="input-value">{{ formatCurrency(loanAmount) }}</span>
                            </div>
                            <input type="range" v-model.number="loanAmount" :min="minAmount" :max="maxAmount" :step="amountStep" class="range-slider">
                            <div class="range-labels">
                                <span>{{ formatCurrency(minAmount) }}</span>
                                <span>{{ formatCurrency(maxAmount) }}</span>
                            </div>
                        </div>

                        <!-- Term Slider -->
                        <div class="calculator-input">
                            <div class="input-header">
                                <label>{{ __('Term') }}</label>
                                <span class="input-value">{{ loanTerm }} {{ __('months') }}</span>
                            </div>
                            <input type="range" v-model.number="loanTerm" :min="minTerm" :max="maxTerm" class="range-slider">
                            <div class="range-labels">
                                <span>{{ minTerm }} мес.</span>
                                <span>{{ maxTerm }} мес.</span>
                            </div>
                        </div>

                        <!-- Monthly Payment Preview -->
                        <div class="payment-preview">
                            <div class="payment-label text-light">{{ __('Approximate monthly payment') }}</div>
                            <div class="payment-amount text-white">{{ formatCurrency(monthlyPayment) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Bar -->
        <div class="filters-bar">
            <div class="container">
                <div class="filters-row">
                    <!-- Purpose Filter -->
                    <div class="filter-group">
                        <select v-model="purpose" @change="applyFilters" class="filter-select">
                            <option v-for="opt in purposeOptions" :key="opt.value" :value="opt.value">
                                {{ opt.label }}
                            </option>
                        </select>
                    </div>

                    <!-- Organization Filter -->
                    <div class="filter-group">
                        <select v-model="organization" @change="applyFilters" class="filter-select">
                            <option value="">{{ __('All organizations') }}</option>
                            <option v-for="org in props.organizations" :key="org.uuid" :value="org.uuid">
                                {{ getTranslation(org.name) }}
                            </option>
                        </select>
                    </div>

                    <!-- Sort -->
                    <div class="filter-group ms-auto">
                        <select v-model="sort" class="filter-select sort-select">
                            <option v-for="opt in sortOptions" :key="opt.value" :value="opt.value">
                                {{ opt.label }}
                            </option>
                        </select>
                    </div>

                    <!-- Clear -->
                    <button v-if="purpose || organization" @click="clearFilters" class="clear-btn">
                        <i class="fas fa-times"></i> {{ __('Clear') }}
                    </button>
                </div>

                <!-- Results count -->
                <div class="results-info">
                    {{ __('Found') }} <strong>{{ props.products.data.length }}</strong> {{ __('offers') }}
                </div>
            </div>
        </div>

        <!-- Products List -->
        <div class="products-section">
            <div class="container">
                <div v-if="props.products.data.length > 0" class="products-list">
                    <CreditCard v-for="product in props.products.data" :key="product.uuid" :product="product" :loan-amount="loanAmount" :loan-term="loanTerm" />
                </div>

                <!-- Empty State -->
                <div v-else class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>{{ __('No credit offers found') }}</h3>
                    <p>{{ __('Try adjusting your filters or loan parameters') }}</p>
                    <button @click="clearFilters" class="btn-primary">
                        {{ __('Reset filters') }}
                    </button>
                </div>

                <!-- Pagination -->
                <nav v-if="props.products.links && props.products.links.length > 3" class="pagination-nav">
                    <ul class="pagination">
                        <li v-for="link in props.products.links" :key="link.label" :class="['page-item', { active: link.active, disabled: !link.url }]">
                            <a v-if="link.url" class="page-link" :href="link.url" v-html="link.label"></a>
                            <span v-else class="page-link" v-html="link.label"></span>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </GuestLayout>
</template>
