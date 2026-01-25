<script setup lang="ts">
import { computed, onMounted, onBeforeUnmount, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useExchangeRates } from '@/composables/useExchangeRates';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();
const page = usePage();
const { exchangeRates, loading, error, hasRates, fetchExchangeRates } = useExchangeRates();

onMounted(() => {
    fetchExchangeRates();
});

// Watch for locale changes and refetch rates
watch(
    () => page.props.locale,
    (newLocale) => {
        if (newLocale) {
            fetchExchangeRates();
        }
    }
);
const tickerViewport = ref<HTMLElement | null>(null);
const scrollingContent = ref<HTMLElement | null>(null);
const isHovered = ref(false);
let rafId: number | null = null;
let wheelHandler: ((e: WheelEvent) => void) | null = null;

onMounted(() => {
    fetchExchangeRates();
});

const duplicatedRates = computed(() => {
    const rates = exchangeRates.value?.rates?.slice(0, 8) ?? [];
    return [...rates, ...rates];
});

const formatRate = (rate: number, unit: number): string => {
    if (unit === 1) {
        return rate.toFixed(4);
    }
    return (rate / unit).toFixed(4);
};

const getCurrencyInfo = (code: number | string): { code: string; flag: string } => {
    // Map ISO 4217 numeric code to { alpha3, country_code_2_letter }
    const map: Record<string, { code: string; flag: string }> = {
        '840': { code: 'USD', flag: 'us' },
        '978': { code: 'EUR', flag: 'eu' },
        '643': { code: 'RUB', flag: 'ru' },
        '810': { code: 'RUB', flag: 'ru' },
        '156': { code: 'CNY', flag: 'cn' },
        '398': { code: 'KZT', flag: 'kz' },
        '980': { code: 'UAH', flag: 'ua' },
        '498': { code: 'MDL', flag: 'md' }, // MDL (User specified 498)
        '986': { code: 'MDL', flag: 'md' }, // Keeping 986 just in case
        '981': { code: 'KGS', flag: 'kg' }, // Kyrgyzstan
        '860': { code: 'UZS', flag: 'uz' }, // Uzbekistan
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
        '36': { code: 'AUD', flag: 'au' },  // 036 -> 36
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
        '971': { code: 'AFN', flag: 'af' }, // AFN (User specified 971)
        '4': { code: 'AFN', flag: 'af' },   // 004 -> 4
        '51': { code: 'AMD', flag: 'am' },  // 051 -> 51
        '944': { code: 'AZN', flag: 'az' },
        '934': { code: 'TMT', flag: 'tm' },
        '784': { code: 'AED', flag: 'ae' },
        '682': { code: 'SAR', flag: 'sa' },
    };
    
    return map[String(code)] ?? { code: String(code), flag: '' };
};
</script>

<template>
    <div class="exchange-rate-card">
        <div class="card-header">
            <h3 class="cart-title fs-6">{{ __('Exchange Rates') }}</h3>
            <span class="date">{{ new Date().toLocaleDateString('ru-RU') }}</span>
        </div>
        
        <div v-if="loading" class="text-center py-3">
            <span class="loader"></span>
        </div>
        
        <div v-else-if="error" class="text-center text-danger py-3">
            {{ error }}
        </div>

        <div v-else-if="hasRates" class="rates-table-container">
            <table class="rates-table">
                <thead>
                    <tr>
                        <th>{{ __('Currency') }}</th>
                        <th class="text-right">{{ __('Rate') }} (TJS)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="rate in exchangeRates.rates" :key="rate.code">
                        <td>
                            <div class="currency-info-row">
                                <img 
                                    v-if="getCurrencyInfo(rate.code).flag"
                                    :src="`https://flagcdn.com/w40/${getCurrencyInfo(rate.code).flag}.png`" 
                                    :alt="getCurrencyInfo(rate.code).code"
                                    class="currency-flag"
                                >
                                <div class="currency-text">
                                    <span class="currency-code">{{ getCurrencyInfo(rate.code).code }}</span>
                                    <span class="currency-name">{{ rate.name }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="text-right font-weight-bold">
                            {{ formatRate(rate.rate, rate.unit) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<style scoped>
.exchange-rate-card {
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden; /* Ensure card respects boundary */
}

.rates-table-container {
    overflow-y: auto;
    flex-grow: 1;
    /* Custom scrollbar for better look */
    scrollbar-width: thin;
    padding-right: 4px; /* Avoid scrollbar overlapping content */
}

.rates-table-container::-webkit-scrollbar {
    width: 4px;
}

.rates-table-container::-webkit-scrollbar-thumb {
    background-color: #ddd;
    border-radius: 4px;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px; /* Slightly reduced margin */
    flex-shrink: 0; /* Header shouldn't shrink */
}

.card-header h3 {
    font-size: 18px;
    font-weight: 700;
    margin: 0;
    color: #333;
}

.date {
    font-size: 12px;
    color: #888;
}

.rates-table {
    width: 100%;
    border-collapse: collapse;
}

.rates-table th {
    font-size: 12px;
    color: #888;
    font-weight: 500;
    padding-bottom: 8px;
    border-bottom: 1px solid #eee;
}

.rates-table td {
    padding: 8px 0;
    font-size: 14px;
    color: #333;
    border-bottom: 1px solid #f5f5f5;
}

.rates-table tr:last-child td {
    border-bottom: none;
}

.currency-info-row {
    display: flex;
    align-items: center;
    gap: 12px;
}

.currency-flag {
    width: 24px;
    height: auto;
    border-radius: 2px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.currency-text {
    display: flex;
    flex-direction: column;
    line-height: 1.2;
}

.currency-code {
    font-weight: 600;
    font-size: 14px;
}

.currency-name {
    font-size: 11px;
    color: #999;
}

.text-right {
    text-align: right;
}

.font-weight-bold {
    font-weight: 600;
}
</style>
