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
        '980': { code: 'UAH', flag: 'ua' },
        '498': { code: 'MDL', flag: 'md' },
        '986': { code: 'MDL', flag: 'md' },
        '981': { code: 'KGS', flag: 'kg' },
        '860': { code: 'UZS', flag: 'uz' },
        '356': { code: 'INR', flag: 'in' },
        '364': { code: 'IRR', flag: 'ir' },
        '368': { code: 'IQD', flag: 'iq' },
        '376': { code: 'ILS', flag: 'il' },
        '392': { code: 'JPY', flag: 'jp' },
        '380': { code: 'JPY', flag: 'jp' },
        '756': { code: 'CHF', flag: 'ch' },
        '826': { code: 'GBP', flag: 'gb' },
        '949': { code: 'TRY', flag: 'tr' },
        '933': { code: 'BYN', flag: 'by' },
        '972': { code: 'TJS', flag: 'tj' },
        '417': { code: 'KGS', flag: 'kg' },
        '36': { code: 'AUD', flag: 'au' },
        '124': { code: 'CAD', flag: 'ca' },
        '208': { code: 'DKK', flag: 'dk' },
        '344': { code: 'HKD', flag: 'hk' },
        '352': { code: 'ISK', flag: 'is' },
        '410': { code: 'KRW', flag: 'kr' },
        '414': { code: 'KWD', flag: 'kw' },
        '458': { code: 'MYR', flag: 'my' },
        '578': { code: 'NOK', flag: 'no' },
        '752': { code: 'SEK', flag: 'se' },
        '702': { code: 'SGD', flag: 'sg' },
        '764': { code: 'THB', flag: 'th' },
        '946': { code: 'RON', flag: 'ro' },
        '975': { code: 'BGN', flag: 'bg' },
        '977': { code: 'BAM', flag: 'ba' },
        '985': { code: 'PLN', flag: 'pl' },
        '496': { code: 'MNT', flag: 'mn' },
        '586': { code: 'PKR', flag: 'pk' },
        '971': { code: 'AFN', flag: 'af' },
        '4': { code: 'AFN', flag: 'af' },
        '51': { code: 'AMD', flag: 'am' },
        '944': { code: 'AZN', flag: 'az' },
        '934': { code: 'TMT', flag: 'tm' },
        '784': { code: 'AED', flag: 'ae' },
        '682': { code: 'SAR', flag: 'sa' },
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

    // Intl.NumberFormat requires a 3-letter currency code (ISO 4217).
    // If info.code is numeric (unknown), it will throw RangeError.
    // Fallback to TJS or a simple number if code is not alpha-3.
    const currencyCode = /^[A-Z]{3}$/i.test(info.code) ? info.code : 'TJS';

    return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: currencyCode === 'Rub' ? 'RUB' : currencyCode,
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

                <div class="row g-4">
                    <!-- Calculator Sidebar -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 p-4 sticky-lg-top" style="top: 2rem; z-index: 10;">
                            <h3 class="fs-4 fw-bold mb-4">{{ __('Currency Converter') }}</h3>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">{{ __('Amount') }}</label>
                                <div class="input-group input-group-lg">
                                    <input type="number" v-model="amount" class="form-control fw-bold border-end-0" min="1">
                                    <span class="input-group-text bg-white border-start-0 text-muted small">{{ fromCurrency === 'TJS' ? 'TJS' : getCurrencyInfo(fromCurrency).code }}</span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">{{ __('From') }}</label>
                                <select v-model="fromCurrency" class="form-select form-select-lg">
                                    <option v-for="c in currencies" :key="c.code" :value="c.code">
                                        {{ getCurrencyInfo(c.code).code }} - {{ c.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="text-center my-3">
                                <i class="fa-solid fa-arrow-down-long fs-3 text-muted opacity-50"></i>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">{{ __('To') }}</label>
                                <select v-model="toCurrency" class="form-select form-select-lg">
                                    <option v-for="c in currencies" :key="c.code" :value="c.code">
                                        {{ getCurrencyInfo(c.code).code }} - {{ c.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="result-box p-3 rounded-4 bg-primary bg-opacity-10 border border-primary border-opacity-10 text-center">
                                <label class="d-block text-muted small mb-1">{{ __('Final Result') }}</label>
                                <div class="fs-3 fw-bold text-primary">
                                    {{ formatCurrency(calculatedResult, toCurrency) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rates Table Main -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 p-4">
                            <ExchangeRateTable />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
.form-control-lg,
.form-select-lg {
    font-size: 1.05rem;
    border-radius: 12px;
}

.result-box {
    transition: all 0.3s ease;
}

.input-group-text {
    border-radius: 0 12px 12px 0;
}

.input-group .form-control {
    border-radius: 12px 0 0 12px;
}

/* Ensure table component fills the space nicely */
:deep(.exchange-rate-card) {
    box-shadow: none !important;
    padding: 0 !important;
}
</style>
