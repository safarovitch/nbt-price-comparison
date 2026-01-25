import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

interface ExchangeRate {
    name: string;
    code: number;
    unit: number;
    rate: number;
}

interface ExchangeRateData {
    pubDate: string;
    description: string;
    rates: ExchangeRate[];
}

interface ExchangeRateResponse {
    success: boolean;
    data: ExchangeRateData;
    message?: string;
}

const exchangeRates = ref<ExchangeRateData | null>(null);
const loading = ref<boolean>(false);
const error = ref<string | null>(null);

const fetchJson = async <T>(url: string): Promise<T> => {
    const response = await fetch(url, {
        headers: { Accept: 'application/json' },
    });

    if (!response.ok) {
        throw new Error(`Failed to fetch: ${response.status}`);
    }

    return response.json();
};

export const useExchangeRates = () => {
    const { props } = usePage<{ locale: string }>();

    const fetchExchangeRates = async (): Promise<void> => {
        try {
            loading.value = true;
            error.value = null;

            const response = await fetchJson<ExchangeRateResponse>('/api/exchange-rates');

            if (response.success && response.data) {
                exchangeRates.value = response.data;
            } else {
                throw new Error(response.message || 'Failed to fetch exchange rates');
            }
        } catch (err) {
            error.value = err instanceof Error ? err.message : 'Failed to fetch exchange rates';
            exchangeRates.value = null;
        } finally {
            loading.value = false;
        }
    };

    const clearError = (): void => {
        error.value = null;
    };

    const hasRates = computed<boolean>(() => {
        return exchangeRates.value !== null && exchangeRates.value.rates.length > 0;
    });

    return {
        exchangeRates,
        loading,
        error,
        hasRates,
        fetchExchangeRates,
        clearError,
    };
};
