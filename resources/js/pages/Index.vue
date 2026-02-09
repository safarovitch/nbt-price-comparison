<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import GuestLayout from '@/layouts/GuestLayout.vue';
import ExchangeRateTable from '@/components/ExchangeRateTable.vue';
import MarketMonitoring from '@/components/MarketMonitoring.vue';
import ServicesSection from '@/components/ServicesSection.vue';
import LatestComments from '@/components/LatestComments.vue';
import LatestNews from '@/components/LatestNews.vue';
import GlobalSearch from '@/components/GlobalSearch.vue';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();

interface MarketMonitoringData {
    credits: Array<{
        uuid: string;
        name: string;
        rate: number;
        rateMax: number;
        termMin: number;
        termMax: number;
        organization: {
            uuid: string;
            name: string;
            logo: string | null;
        } | null;
    }>;
    deposits: Array<{
        uuid: string;
        name: string;
        rate: number;
        rateMin: number;
        termMin: number;
        termMax: number;
        organization: {
            uuid: string;
            name: string;
            logo: string | null;
        } | null;
    }>;
}

const props = defineProps<{
    marketMonitoring?: MarketMonitoringData;
    latestComments?: Array<any>;
    latestNews?: Array<any>;
}>();
</script>

<template>

    <Head title="Home - Price Comparison" />

    <GuestLayout>
        <!-- Hero Services Section Start -->
        <div class="hero-services">
            <div class="container">

                <div class="section-header mb-4 text-center mb-5 mt-5">
                    <h2>{{ __('Compare. Choose. Save.') }}</h2>
                </div>

                <GlobalSearch />

                <div class="services-grid">
                    <!-- Exchange Rate Table (Spans 2x2) -->
                    <div class="service-card exchange-table-card">
                        <ExchangeRateTable />
                    </div>

                    <!-- 1. Consumer Loans -->
                    <a href="/credits" class="service-card short">
                        <div class="card-content">
                            <h3>{{ __('Consumer Loans') }}</h3>
                        </div>
                        <div class="card-icon bottom-right">
                            <img src="/storage/images/icons/credits-icon.png" alt="Consumer Loans">
                        </div>
                    </a>

                    <!-- 2. Car Loans -->
                    <div class="service-card short">
                        <div class="card-content">
                            <h3>{{ __('Car Loans') }}</h3>
                        </div>
                        <div class="card-icon bottom-right">
                            <img src="/storage/images/icons/casco-icon.png" alt="Car Loans">
                        </div>
                    </div>

                    <!-- 3. E-wallets -->
                    <div class="service-card short">
                        <div class="card-content">
                            <h3>{{ __('E-wallets') }}</h3>
                        </div>
                        <div class="card-icon bottom-right">
                            <img src="/storage/images/icons/deposit-icon.png" alt="E-wallets">
                        </div>
                    </div>

                    <!-- 4. Credit Cards -->
                    <div class="service-card short">
                        <div class="card-content">
                            <h3>{{ __('Credit Cards') }}</h3>
                        </div>
                        <div class="card-icon bottom-right">
                            <img src="/storage/images/icons/credit-rating-icon.png" alt="Credit Cards">
                        </div>
                    </div>

                    <!-- 5. Insurance -->
                    <div class="service-card short">
                        <div class="card-content">
                            <h3>{{ __('Insurance') }}</h3>
                        </div>
                        <div class="card-icon bottom-right">
                            <img src="/storage/images/icons/insurance-icon.png" alt="Insurance">
                        </div>
                    </div>

                    <!-- 6. Migrant Services -->
                    <div class="service-card short">
                        <div class="card-content">
                            <h3>{{ __('Migrant Services') }}</h3>
                        </div>
                        <div class="card-icon bottom-right">
                            <img src="/storage/images/icons/tourist-insurance-icon.png" alt="Migrant Services">
                        </div>
                    </div>

                    <!-- 7. Current Accounts -->
                    <div class="service-card short">
                        <div class="card-content">
                            <h3>{{ __('Current Accounts') }}</h3>
                        </div>
                        <div class="card-icon bottom-right">
                            <img src="/storage/images/icons/deposit-icon.png" alt="Current Accounts">
                        </div>
                    </div>

                    <!-- 8. Mortgage Programs -->
                    <div class="service-card short">
                        <div class="card-content">
                            <h3>{{ __('Mortgage Programs') }}</h3>
                        </div>
                        <div class="card-icon bottom-right">
                            <img src="/storage/images/icons/house-financing-icon.png" alt="Mortgage">
                        </div>
                    </div>

                    <!-- 9. Deposits -->
                    <div class="service-card short">
                        <div class="card-content">
                            <h3>{{ __('Deposits') }}</h3>
                        </div>
                        <div class="card-icon bottom-right">
                            <img src="/storage/images/icons/deposit-icon.png" alt="Deposits">
                        </div>
                    </div>

                    <!-- 10. All Services -->
                    <div class="service-card short bg-grey">
                        <div class="card-content flex-center">
                            <i class="fa-thin fa-grid"></i>
                            <h3>{{ __('All Services') }}</h3>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- Hero Services Section End -->

        <!-- Our Services Section Start -->
        <div class="services-section py-5 bg-light mt-5">
            <div class="container">
                <div class="section-header mb-4 d-flex justify-content-between">
                    <h2>{{ __('Our Services') }}</h2>
                    <p>{{ __('Use our tools for your financial calculations.') }}</p>
                </div>

                <ServicesSection />
            </div>
        </div>
        <!-- Our Services Section End -->

        <!-- Market Monitoring Section Start -->
        <MarketMonitoring :credits="props.marketMonitoring?.credits ?? []" :deposits="props.marketMonitoring?.deposits ?? []" />
        <!-- Market Monitoring Section End -->

        <!-- Latest News Section Start -->
        <LatestNews :posts="props.latestNews ?? []" />
        <!-- Latest News Section End -->

        <!-- Latest Comments Section Start -->
        <LatestComments :comments="props.latestComments ?? []" />
        <!-- Latest Comments Section End -->
    </GuestLayout>
</template>
