<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { getTranslation } from '@/utils/translations';
import { useTrans } from '@/composables/useTrans';

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
    product: Product;
    loanAmount?: number;
    loanTerm?: number;
}

const props = withDefaults(defineProps<Props>(), {
    loanAmount: 50000,
    loanTerm: 12,
});

const purposeLabels: Record<string, string> = {
    consumer: 'Потребительский',
    auto: 'Автокредит',
    mortgage: 'Ипотека',
    business: 'Бизнес',
    education: 'Образование',
    personal: 'Личные нужды',
};

// Calculate monthly payment based on product's rate
const monthlyPayment = computed(() => {
    const rate = (props.product.interest_rate_min ?? 20) / 100 / 12;
    const n = props.loanTerm;
    const p = props.loanAmount;
    if (rate === 0) return Math.round(p / n);
    const payment = p * (rate * Math.pow(1 + rate, n)) / (Math.pow(1 + rate, n) - 1);
    return Math.round(payment);
});

const formatRate = computed(() => {
    const min = props.product.interest_rate_min;
    const max = props.product.interest_rate_max;

    if (min === null && max === null) return '—';
    if (min === max || max === null) return `${min}%`;
    if (min === null) return `до ${max}%`;
    return `от ${min}%`;
});

const rateRange = computed(() => {
    const min = props.product.interest_rate_min;
    const max = props.product.interest_rate_max;

    if (min === null && max === null) return null;
    if (min === max || min === null || max === null) return null;
    return `до ${max}%`;
});

const formatTerm = computed(() => {
    const min = props.product.term_months_min;
    const max = props.product.term_months_max;

    if (min === null && max === null) return '—';
    if (max === null) return `${min} мес.`;
    return `до ${max} мес.`;
});

const formatAmount = computed(() => {
    const max = props.product.max_amount;
    if (max === null) return '—';
    return `до ${max.toLocaleString('ru-RU')} с.`;
});

const formatCurrency = (value: number) => {
    return value.toLocaleString('ru-RU') + ' с.';
};

const purposeLabel = computed(() => {
    return props.product.purpose ? purposeLabels[props.product.purpose] || props.product.purpose : null;
});

const productFeatures = computed(() => {
    const features = [];
    if (props.product.min_amount) {
        features.push(`От ${props.product.min_amount.toLocaleString('ru-RU')} с.`);
    }
    if (props.product.term_months_min) {
        features.push(`Срок от ${props.product.term_months_min} мес.`);
    }
    return features;
});
</script>

<template>
    <div class="credit-card">
        <!-- Left: Organization -->
        <div class="card-org">
            <div class="org-logo">
                <i class="fas fa-university"></i>
            </div>
            <div class="org-details">
                <h4 class="org-name">{{ getTranslation(product.organization.name) }}</h4>
                <p class="product-name">{{ getTranslation(product.name) }}</p>
            </div>
        </div>

        <!-- Center: Key Metrics -->
        <div class="card-metrics">
            <!-- Rate -->
            <div class="metric rate-metric">
                <div class="metric-value-group">
                    <span class="metric-value primary">{{ formatRate }}</span>
                    <span v-if="rateRange" class="metric-range">{{ rateRange }}</span>
                </div>
                <span class="metric-label">{{ __('Rate') }}</span>
            </div>

            <!-- Term -->
            <div class="metric">
                <span class="metric-value">{{ formatTerm }}</span>
                <span class="metric-label">{{ __('Term') }}</span>
            </div>

            <!-- Amount -->
            <div class="metric">
                <span class="metric-value">{{ formatAmount }}</span>
                <span class="metric-label">{{ __('Amount') }}</span>
            </div>

            <!-- Monthly Payment -->
            <div class="metric payment-metric">
                <span class="metric-value">{{ formatCurrency(monthlyPayment) }}</span>
                <span class="metric-label">{{ __('Monthly payment') }}</span>
            </div>
        </div>

        <!-- Right: CTA -->
        <div class="card-actions">
            <button class="btn-apply">
                {{ __('Get offer') }}
                <i class="fas fa-arrow-right"></i>
            </button>
            <Link :href="`/credits/${product.uuid}`" class="btn-details">
                {{ __('Details') }}
            </Link>
        </div>

        <!-- Features Tags (bottom) -->
        <div class="card-features" v-if="purposeLabel || productFeatures.length > 0">
            <span v-if="purposeLabel" class="feature-tag purpose-tag">
                {{ purposeLabel }}
            </span>
            <span v-for="feature in productFeatures" :key="feature" class="feature-tag">
                {{ feature }}
            </span>
        </div>
    </div>
</template>
