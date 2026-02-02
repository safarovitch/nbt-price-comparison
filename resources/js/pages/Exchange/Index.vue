<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useTrans } from '@/composables/useTrans';
import { useExchangeRates } from '@/composables/useExchangeRates';
import ExchangeRateTable from '@/components/ExchangeRateTable.vue';

const { __ } = useTrans();
const { exchangeRates, loading, error, hasRates, fetchExchangeRates } = useExchangeRates();

onMounted(() => {
    fetchExchangeRates();
});

const amount = ref(100);
const fromCurrency = ref('TJS');
const toCurrency = ref('840'); // USD

// Copy of map from ExchangeRateTable (should be shared util ideally)
const getCurrencyInfo = (code: number | string): { code: string; flag: string } => {
    const map: Record<string, { code: string; flag: string }> = {
        '840': { code: 'USD', flag: 'us' },
        '978': { code: 'EUR', flag: 'eu' },
        '643': { code: 'RUB', flag: 'ru' },
        '810': { code: 'RUB', flag: 'ru' },
        '156': { code: 'CNY', flag: 'cn' },
        '398': { code: 'KZT', flag: 'kz' },
        '972': { code: 'TJS', flag: 'tj' }, 
        // ... add others if needed or rely on list
    };
    if (code === 'TJS') return { code: 'TJS', flag: 'tj' };
    return map[String(code)] ?? { code: String(code), flag: '' };
};

const currencies = computed(() => {
    const list = [
        { code: 'TJS', name: 'Somoni', rate: 1, unit: 1 }
    ];
    if (exchangeRates.value?.rates) {
        list.push(...exchangeRates.value.rates.map(r => ({
            code: String(r.code),
            name: r.name,
            rate: r.rate,
            unit: r.unit
        })));
    }
    return list;
});

const calculatedResult = computed(() => {
    if (!amount.value || !fromCurrency.value || !toCurrency.value) return 0;
    
    // Get rates to TJS
    const from = currencies.value.find(c => c.code === fromCurrency.value);
    const to = currencies.value.find(c => c.code === toCurrency.value);

    if (!from || !to) return 0;

    // Convert From -> TJS
    // Rate is TJS per Unit
    // Value in TJS = Amount * (Rate / Unit)
    const valInTJS = amount.value * (from.rate / from.unit);

    // Convert TJS -> To
    // Value in To = ValInTJS / (Rate / Unit)
    const valInTo = valInTJS / (to.rate / to.unit);

    return valInTo;
});

const formatCurrency = (val: number, code: string) => {
    const info = getCurrencyInfo(code);
    return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: info.code === 'Rub' ? 'RUB' : info.code, // Rub -> RUB fix if needed
        minimumFractionDigits: 2,
        currencyDisplay: 'code'
    }).format(val);
};

</script>

<template>
    <Head :title="__('Exchange Rates')" />
    
    <GuestLayout>
        <div class="bg-light-gray min-vh-100 py-5">
            <div class="container">
                <div class="mb-4">
                    <Link href="/" class="text-decoration-none text-muted mb-2 d-inline-block">
                        <i class="fa-solid fa-arrow-left me-2"></i> {{ __('Back to Home') }}
                    </Link>
                    <h1 class="fw-bold">{{ __('Exchange Rates') }}</h1>
                </div>

                <!-- Calculator -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-5" id="calculator">
                    <h3 class="fs-4 fw-bold mb-4">{{ __('Currency Converter') }}</h3>
                    <div class="row g-4 align-items-center">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">{{ __('Amount') }}</label>
                            <input type="number" v-model="amount" class="form-control form-control-lg fw-bold" min="1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">{{ __('From') }}</label>
                            <select v-model="fromCurrency" class="form-select form-select-lg">
                                <option v-for="c in currencies" :key="c.code" :value="c.code">
                                    {{ getCurrencyInfo(c.code).code }} - {{ c.name }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-1 text-center">
                            <i class="fa-solid fa-arrow-right-long fs-3 text-muted d-none d-md-inline-block mt-4"></i>
                            <i class="fa-solid fa-arrow-down-long fs-3 text-muted d-md-none my-2"></i>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">{{ __('To') }}</label>
                            <select v-model="toCurrency" class="form-select form-select-lg">
                                <option v-for="c in currencies" :key="c.code" :value="c.code">
                                    {{ getCurrencyInfo(c.code).code }} - {{ c.name }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2 text-end">
                            <label class="form-label d-block text-muted small mb-1">{{ __('Result') }}</label>
                            <div class="fs-4 fw-bold text-primary">
                                {{ formatCurrency(calculatedResult, toCurrency) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Full Exchange Table -->
                <div class="row">
                    <div class="col-12">
                        <ExchangeRateTable />
                    </div>
                </div>

            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
.form-control-lg, .form-select-lg {
    font-size: 1.1rem;
}
</style>
