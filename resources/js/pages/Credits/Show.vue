<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import GuestLayout from '@/layouts/GuestLayout.vue';
import CreditCard from '@/components/CreditCard.vue';
import ApplicationFormModal from '@/components/ApplicationFormModal.vue';
import { useTrans } from '@/composables/useTrans';
import { computed, ref } from 'vue';
import { getTranslation } from '@/utils/translations';

const { __ } = useTrans();

// Modal state
const showApplicationModal = ref(false);

const openApplicationModal = () => {
    showApplicationModal.value = true;
};

const closeApplicationModal = () => {
    showApplicationModal.value = false;
};

const handleApplicationSuccess = (application: any) => {
    console.log('Application submitted:', application);
    // Could show a toast or redirect
};

interface Organization {
    uuid: string;
    name: Record<string, string>;
    logo?: string;
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
    rate_tiers: any[] | null;
    fee_structure: Record<string, number> | null;
    fees: number | null;
    required_documents: string[] | null;
    min_age: number | null;
    max_age: number | null;
    online_application: boolean | null;
    approval_time: string | null;
    grace_period_days: number | null;
    organization: Organization;
}

interface Props {
    product: Product;
    similarProducts: Product[];
    competingProducts: Product[];
}

const props = defineProps<Props>();

const purposeLabels: Record<string, string> = {
    consumer: 'Потребительский кредит',
    auto: 'Автокредит',
    mortgage: 'Ипотека',
    business: 'Бизнес-кредит',
    education: 'Образовательный кредит',
    personal: 'Кредит на личные нужды',
};

const formatRate = computed(() => {
    const min = props.product.interest_rate_min;
    const max = props.product.interest_rate_max;
    if (min === null && max === null) return '—';
    if (min === max || max === null) return `${min}%`;
    if (min === null) return `до ${max}%`;
    return `${min}% – ${max}%`;
});

const formatTerm = computed(() => {
    const min = props.product.term_months_min;
    const max = props.product.term_months_max;
    if (min === null && max === null) return '—';
    if (min === max || max === null) return `${min} мес.`;
    if (min === null) return `до ${max} мес.`;
    return `${min} – ${max} мес.`;
});

const formatAmount = computed(() => {
    const min = props.product.min_amount;
    const max = props.product.max_amount;
    const formatNum = (n: number) => n.toLocaleString('ru-RU') + ' с.';
    if (min === null && max === null) return '—';
    if (max === null) return `от ${formatNum(min!)}`;
    if (min === null) return `до ${formatNum(max)}`;
    return `${formatNum(min)} – ${formatNum(max)}`;
});

const ageRequirement = computed(() => {
    const min = props.product.min_age;
    const max = props.product.max_age;
    if (min === null && max === null) return null;
    if (max === null) return `от ${min} лет`;
    if (min === null) return `до ${max} лет`;
    return `${min} – ${max} лет`;
});

const feesList = computed(() => {
    const fees: { label: string; value: string }[] = [];
    const structure = props.product.fee_structure;

    if (structure) {
        if (structure.origination) fees.push({ label: 'Комиссия за выдачу', value: `${structure.origination}%` });
        if (structure.monthly) fees.push({ label: 'Ежемесячная комиссия', value: `${structure.monthly}%` });
        if (structure.annual) fees.push({ label: 'Годовое обслуживание', value: `${structure.annual} с.` });
        if (structure.early_repayment) fees.push({ label: 'Досрочное погашение', value: `${structure.early_repayment}%` });
        if (structure.service) fees.push({ label: 'Сервисный сбор', value: `${structure.service}%` });
    }

    if (fees.length === 0 && props.product.fees) {
        fees.push({ label: 'Комиссия', value: `${props.product.fees}%` });
    }

    return fees;
});

const specifications = computed(() => {
    const specs: { label: string; value: string }[] = [];

    specs.push({ label: 'Процентная ставка', value: formatRate.value });
    specs.push({ label: 'Срок кредитования', value: formatTerm.value });
    specs.push({ label: 'Сумма кредита', value: formatAmount.value });

    if (ageRequirement.value) {
        specs.push({ label: 'Возраст заёмщика', value: ageRequirement.value });
    }

    if (props.product.online_application !== null) {
        specs.push({ label: 'Онлайн-заявка', value: props.product.online_application ? 'Да' : 'Нет' });
    }

    if (props.product.approval_time) {
        specs.push({ label: 'Срок рассмотрения', value: props.product.approval_time });
    }

    if (props.product.grace_period_days) {
        specs.push({ label: 'Льготный период', value: `${props.product.grace_period_days} дней` });
    }

    return specs;
});
</script>

<template>

    <Head :title="getTranslation(product.name) + ' — Кредит'" />

    <GuestLayout>
        <!-- Breadcrumb -->
        <div class="breadcrumb-bar">
            <div class="container">
                <Link href="/credits" class="breadcrumb-link">
                    <i class="fas fa-arrow-left"></i> {{ __('Back to credits') }}
                </Link>
            </div>
        </div>

        <!-- Product Header -->
        <div class="product-header">
            <div class="container">
                <div class="header-content">
                    <div class="org-info">
                        <div class="org-logo">
                            <i class="fas fa-university"></i>
                        </div>
                        <div class="org-details">
                            <span class="org-name">{{ getTranslation(product.organization.name) }}</span>
                            <h1 class="product-title">{{ getTranslation(product.name) }}</h1>
                            <span v-if="product.purpose" class="purpose-badge">
                                {{ purposeLabels[product.purpose] || product.purpose }}
                            </span>
                        </div>
                    </div>

                    <div class="header-cta">
                        <button class="btn-apply-large" @click="openApplicationModal">
                            <i class="fas fa-paper-plane"></i>
                            {{ __('Submit application') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="metrics-section">
            <div class="container">
                <div class="metrics-grid">
                    <div class="metric-card primary">
                        <div class="metric-icon">
                            <i class="fas fa-percent"></i>
                        </div>
                        <div class="metric-content">
                            <span class="metric-value">{{ formatRate }}</span>
                            <span class="metric-label">{{ __('Interest rate') }}</span>
                        </div>
                    </div>

                    <div class="metric-card">
                        <div class="metric-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="metric-content">
                            <span class="metric-value">{{ formatTerm }}</span>
                            <span class="metric-label">{{ __('Term') }}</span>
                        </div>
                    </div>

                    <div class="metric-card">
                        <div class="metric-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div class="metric-content">
                            <span class="metric-value">{{ formatAmount }}</span>
                            <span class="metric-label">{{ __('Loan amount') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="product-content">
            <div class="container">
                <div class="content-grid">
                    <!-- Left Column: Details -->
                    <div class="details-column">
                        <!-- Description -->
                        <div v-if="product.description" class="section-card">
                            <h3 class="section-title">{{ __('Description') }}</h3>
                            <p class="description-text">{{ getTranslation(product.description) }}</p>
                        </div>

                        <!-- Specifications -->
                        <div class="section-card">
                            <h3 class="section-title">{{ __('Credit terms') }}</h3>
                            <div class="specs-list">
                                <div v-for="spec in specifications" :key="spec.label" class="spec-row">
                                    <span class="spec-label">{{ spec.label }}</span>
                                    <span class="spec-value">{{ spec.value }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Fees -->
                        <div v-if="feesList.length > 0" class="section-card">
                            <h3 class="section-title">{{ __('Fees and commissions') }}</h3>
                            <div class="specs-list">
                                <div v-for="fee in feesList" :key="fee.label" class="spec-row">
                                    <span class="spec-label">{{ fee.label }}</span>
                                    <span class="spec-value">{{ fee.value }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Rate Tiers -->
                        <div v-if="product.rate_tiers && product.rate_tiers.length > 0" class="section-card">
                            <h3 class="section-title">{{ __('Rate tiers') }}</h3>
                            <div class="rate-tiers-table">
                                <div class="tier-header">
                                    <span>{{ __('Amount') }}</span>
                                    <span>{{ __('Rate') }}</span>
                                </div>
                                <div v-for="(tier, index) in product.rate_tiers" :key="index" class="tier-row">
                                    <span>
                                        {{ tier.min_amount?.toLocaleString('ru-RU') || '—' }} –
                                        {{ tier.max_amount?.toLocaleString('ru-RU') || '—' }} с.
                                    </span>
                                    <span class="tier-rate">{{ tier.interest_rate }}%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Required Documents -->
                        <div v-if="product.required_documents && product.required_documents.length > 0" class="section-card">
                            <h3 class="section-title">{{ __('Required documents') }}</h3>
                            <ul class="documents-list">
                                <li v-for="doc in product.required_documents" :key="doc">
                                    <i class="fas fa-check-circle"></i>
                                    {{ doc }}
                                </li>
                            </ul>
                        </div>

                        <!-- Eligibility -->
                        <div v-if="product.eligibility" class="section-card">
                            <h3 class="section-title">{{ __('Eligibility requirements') }}</h3>
                            <p class="eligibility-text">{{ getTranslation(product.eligibility) }}</p>
                        </div>
                    </div>

                    <!-- Right Column: Sidebar -->
                    <div class="sidebar-column">
                        <!-- Apply Card -->
                        <div class="apply-card">
                            <h4>{{ __('Interested in this offer?') }}</h4>
                            <p>{{ __('Submit an application online and get a decision quickly') }}</p>
                            <button class="btn-apply-full" @click="openApplicationModal">
                                {{ __('Submit application') }}
                            </button>
                            <div class="apply-note">
                                <i class="fas fa-shield-alt"></i>
                                {{ __('Your data is protected') }}
                            </div>
                        </div>

                        <!-- Organization Card -->
                        <div class="org-card">
                            <div class="org-card-header">
                                <div class="org-card-logo">
                                    <i class="fas fa-university"></i>
                                </div>
                                <span class="org-card-name">{{ getTranslation(product.organization.name) }}</span>
                            </div>
                            <Link :href="`/credits?organization=${product.organization.uuid}`" class="org-card-link">
                                {{ __('All products from this organization') }}
                                <i class="fas fa-chevron-right"></i>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Similar Products -->
        <div v-if="similarProducts.length > 0" class="related-section">
            <div class="container">
                <h2 class="related-title">{{ __('Other products from') }} {{ getTranslation(product.organization.name) }}</h2>
                <div class="related-list">
                    <CreditCard v-for="p in similarProducts" :key="p.uuid" :product="p" />
                </div>
            </div>
        </div>

        <!-- Competing Products -->
        <div v-if="competingProducts.length > 0" class="related-section alt-bg">
            <div class="container">
                <h2 class="related-title">{{ __('Compare with other offers') }}</h2>
                <div class="related-list">
                    <CreditCard v-for="p in competingProducts" :key="p.uuid" :product="p" />
                </div>
            </div>
        </div>

        <!-- Application Form Modal -->
        <!-- Application Form Modal -->
        <ApplicationFormModal v-if="showApplicationModal" :product="product" @close="closeApplicationModal" @success="handleApplicationSuccess" />
    </GuestLayout>
</template>
