<script setup lang="ts">
import { onMounted, onUnmounted, ref, nextTick, computed, watch } from 'vue';
import { useTrans } from '@/composables/useTrans';
import * as am5 from '@amcharts/amcharts5';
import * as am5xy from '@amcharts/amcharts5/xy';
import am5themes_Animated from '@amcharts/amcharts5/themes/Animated';

const { __ } = useTrans();

interface ProductData {
    uuid: string;
    name: string;
    rate: number;
    rateMax?: number;
    rateMin?: number;
    termMin: number;
    termMax: number;
    organization: {
        uuid: string;
        name: string;
        logo: string | null;
    } | null;
}

const props = defineProps<{
    credits: ProductData[];
    deposits: ProductData[];
}>();

const currentMonth = computed(() => {
    return new Intl.DateTimeFormat(undefined, { month: 'long', year: 'numeric' }).format(new Date()).toUpperCase();
});

const loanChartRoot = ref<am5.Root | null>(null);
const depositChartRoot = ref<am5.Root | null>(null);

const activeLoanPeriod = ref('5 years');
const activeDepositPeriod = ref('1 year');

const loanPeriods = ['6 months', '1 year', '3 years', '5 years'];
const depositPeriods = ['1 month', '3 months', '6 months', '1 year'];

// Helper to convert period string to months
const periodToMonths = (period: string): number => {
    const match = period.match(/(\d+)\s*(month|year)/);
    if (!match) return 12;
    const value = parseInt(match[1]);
    const unit = match[2];
    return unit === 'year' ? value * 12 : value;
};

// Filter credits by selected period
const filteredCredits = computed(() => {
    const targetMonths = periodToMonths(activeLoanPeriod.value);
    return props.credits
        .filter(p => {
            // Match if term range includes the target period
            const min = p.termMin ?? 0;
            const max = p.termMax ?? 999;
            return min <= targetMonths && max >= targetMonths;
        })
        .slice(0, 6);
});

// Filter deposits by selected period
const filteredDeposits = computed(() => {
    const targetMonths = periodToMonths(activeDepositPeriod.value);
    return props.deposits
        .filter(p => {
            const min = p.termMin ?? 0;
            const max = p.termMax ?? 999;
            return min <= targetMonths && max >= targetMonths;
        })
        .slice(0, 5);
});

// Chart colors array
const chartColors = ['#e31e24', '#00a651', '#00aed9', '#006838', '#303e9f', '#ff6b35'];

const initLoanChart = () => {
    if (loanChartRoot.value) loanChartRoot.value.dispose();
    
    const chartDiv = document.getElementById("loan-chart-div");
    if (!chartDiv) return;
    
    const root = am5.Root.new("loan-chart-div");
    root.setThemes([am5themes_Animated.new(root)]);

    const chart = root.container.children.push(
        am5xy.XYChart.new(root, {
            panX: false,
            panY: false,
            wheelX: "none",
            wheelY: "none",
            layout: root.verticalLayout
        })
    );

    const data = filteredCredits.value.map((p, index) => ({
        bank: p.organization?.name || p.name,
        rate: p.rate,
        logo: p.organization?.logo || "/storage/images/icons/bank-placeholder.png",
        color: chartColors[index % chartColors.length]
    }));

    if (data.length === 0) {
        loanChartRoot.value = root;
        return;
    }

    const xAxis = chart.xAxes.push(
        am5xy.CategoryAxis.new(root, {
            categoryField: "bank",
            renderer: am5xy.AxisRendererX.new(root, {
                minGridDistance: 30,
                visible: false
            })
        })
    );
    xAxis.data.setAll(data);
    xAxis.get("renderer").labels.template.set("forceHidden", true);
    xAxis.get("renderer").grid.template.set("forceHidden", true);

    const yAxis = chart.yAxes.push(
        am5xy.ValueAxis.new(root, {
            renderer: am5xy.AxisRendererY.new(root, {
                visible: false
            })
        })
    );
    yAxis.get("renderer").labels.template.set("forceHidden", true);
    yAxis.get("renderer").grid.template.set("forceHidden", true);

    const series = chart.series.push(
        am5xy.ColumnSeries.new(root, {
            name: "Rates",
            xAxis: xAxis,
            yAxis: yAxis,
            valueYField: "rate",
            categoryXField: "bank",
            tooltip: am5.Tooltip.new(root, {
                labelText: "{valueY}%"
            })
        })
    );

    series.columns.template.setAll({
        cornerRadiusTL: 15,
        cornerRadiusTR: 15,
        strokeOpacity: 0,
        width: am5.percent(40),
        fillOpacity: 0.1,
    });

    series.columns.template.adapters.add("fill", (fill, target) => {
        return am5.color((target.dataItem?.dataContext as any).color);
    });

    series.bullets.push(() => {
        return am5.Bullet.new(root, {
            locationY: 1,
            sprite: am5.Label.new(root, {
                text: "{valueY}%",
                fill: am5.color(0x000000),
                centerY: am5.p100,
                centerX: am5.p50,
                populateText: true,
                fontWeight: "bold",
                fontSize: 12,
                dy: -10
            })
        });
    });

    series.bullets.push(() => {
        const container = am5.Container.new(root, {
            centerX: am5.p50,
            centerY: am5.p50,
            y: am5.p100,
            dy: 25
        });

        container.children.push(am5.Circle.new(root, {
            radius: 15,
            fill: am5.color(0xffffff),
            stroke: am5.color(0xeeeeee),
            strokeWidth: 1
        }));

        container.children.push(am5.Picture.new(root, {
            width: 20,
            height: 20,
            centerX: am5.p50,
            centerY: am5.p50,
            src: "{logo}"
        }));

        return am5.Bullet.new(root, {
            locationY: 0,
            sprite: container
        });
    });

    series.data.setAll(data);
    series.appear(1000);
    chart.appear(1000, 100);
    
    loanChartRoot.value = root;
};

const initDepositChart = () => {
    if (depositChartRoot.value) depositChartRoot.value.dispose();

    const chartDiv = document.getElementById("deposit-chart-div");
    if (!chartDiv) return;

    const root = am5.Root.new("deposit-chart-div");
    root.setThemes([am5themes_Animated.new(root)]);

    const chart = root.container.children.push(
        am5xy.XYChart.new(root, {
            panX: false,
            panY: false,
            wheelX: "none",
            wheelY: "none",
            layout: root.verticalLayout
        })
    );

    const data = filteredDeposits.value.map((p, index) => ({
        bank: p.organization?.name || p.name,
        rate: p.rate,
        color: chartColors[index % chartColors.length]
    }));

    if (data.length === 0) {
        depositChartRoot.value = root;
        return;
    }

    const yAxis = chart.yAxes.push(
        am5xy.CategoryAxis.new(root, {
            categoryField: "bank",
            renderer: am5xy.AxisRendererY.new(root, {
                inversed: true,
                minGridDistance: 30,
                visible: false
            })
        })
    );
    yAxis.data.setAll(data);
    yAxis.get("renderer").grid.template.set("forceHidden", true);
    yAxis.get("renderer").labels.template.set("forceHidden", true);

    const xAxis = chart.xAxes.push(
        am5xy.ValueAxis.new(root, {
            renderer: am5xy.AxisRendererX.new(root, {
                visible: false
            })
        })
    );
    xAxis.get("renderer").grid.template.set("forceHidden", true);
    xAxis.get("renderer").labels.template.set("forceHidden", true);

    const series = chart.series.push(
        am5xy.ColumnSeries.new(root, {
            name: "Rates",
            xAxis: xAxis,
            yAxis: yAxis,
            valueXField: "rate",
            categoryYField: "bank",
            tooltip: am5.Tooltip.new(root, {
                labelText: "{valueX}%"
            })
        })
    );

    series.columns.template.setAll({
        height: 8,
        cornerRadiusTL: 4,
        cornerRadiusTR: 4,
        cornerRadiusBL: 4,
        cornerRadiusBR: 4,
        strokeOpacity: 0,
        fill: am5.color(0x00a651),
        fillOpacity: 0.1
    });

    series.columns.template.adapters.add("fill", () => {
        return am5.color(0x00a651);
    });

    series.bullets.push(() => {
        return am5.Bullet.new(root, {
            locationX: 1,
            sprite: am5.Label.new(root, {
                text: "{valueX}%",
                fill: am5.color(0x00aed9),
                centerY: am5.p50,
                centerX: am5.p0,
                dx: 10,
                populateText: true,
                fontWeight: "bold",
                fontSize: 14
            })
        });
    });

    series.data.setAll(data);
    series.appear(1000);
    chart.appear(1000, 100);

    depositChartRoot.value = root;
};

// Watch for period changes to update charts
watch(activeLoanPeriod, () => {
    nextTick(() => initLoanChart());
});

watch(activeDepositPeriod, () => {
    nextTick(() => initDepositChart());
});

onMounted(() => {
    nextTick(() => {
        initLoanChart();
        initDepositChart();
    });
});

onUnmounted(() => {
    if (loanChartRoot.value) loanChartRoot.value.dispose();
    if (depositChartRoot.value) depositChartRoot.value.dispose();
});
</script>

<template>
    <section class="market-monitoring py-5 bg-light-gray">
        <div class="container py-lg-4">
            <div class="d-flex align-items-center mb-5 gap-3">
                <h2 class="fw-bold fs-2 mb-0">{{ __('Market Monitoring') }}</h2>
                <span class="badge bg-dark rounded-pill py-2 px-3 text-uppercase small letter-spacing-1">
                    {{ currentMonth }}
                </span>
            </div>

            <div class="row g-4">
                <!-- 1. Loan Rates -->
                <div class="col-lg-4">
                    <div class="monitoring-card h-100 bg-white p-3 p-xl-4 rounded-4 border-0 shadow-sm">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h3 class="fs-4 fw-bold mb-1">{{ __('Loan Rates') }}</h3>
                                <p class="text-muted small mb-0">{{ __('based on real disbursements') }}</p>
                            </div>
                            <div class="icon-circle bg-light text-dark">
                                <i class="fa-solid fa-money-bill-transfer"></i>
                            </div>
                        </div>

                        <div class="period-selector d-flex gap-2 mb-5">
                            <button v-for="period in loanPeriods" :key="period" 
                                    @click="activeLoanPeriod = period"
                                    class="btn btn-sm rounded-pill px-3 py-2 fw-bold transition-all"
                                    :class="activeLoanPeriod === period ? 'btn-primary' : 'btn-light text-muted'">
                                {{ __(period) }}
                            </button>
                        </div>

                        <div id="loan-chart-div" style="width: 100%; height: 250px;"></div>

                        <div class="mt-auto pt-5 d-flex align-items-center justify-content-between">
                            <button class="btn btn-light rounded-pill w-full">{{ __('Find your rate') }}</button>
                            <p class="text-muted small mb-0 opacity-75 ms-3">
                                {{ __('We will select banks ready to issue a loan') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 2. Profitable Deposits -->
                <div class="col-lg-4">
                    <div class="monitoring-card h-100 bg-white p-3 p-xl-4 rounded-4 border-0 shadow-sm">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h3 class="fs-4 fw-bold mb-1">{{ __('Profitable Deposits') }}</h3>
                                <p class="text-muted small mb-0">{{ __('Checked rates in 24 banks') }}</p>
                            </div>
                            <div class="icon-circle bg-light text-dark">
                                <i class="fa-solid fa-vault"></i>
                            </div>
                        </div>

                        <div class="period-selector d-flex gap-2 mb-5">
                            <button v-for="period in depositPeriods" :key="period" 
                                    @click="activeDepositPeriod = period"
                                    class="btn btn-sm rounded-pill px-3 py-2 fw-bold transition-all"
                                    :class="activeDepositPeriod === period ? 'btn-primary' : 'btn-light text-muted'">
                                {{ __(period) }}
                            </button>
                        </div>

                        <div class="deposit-list">
                            <div v-if="filteredDeposits.length === 0" class="text-muted text-center py-4">
                                {{ __('No deposits available for this period') }}
                            </div>
                            <div v-for="(deposit, index) in filteredDeposits" :key="deposit.uuid" 
                                 class="d-flex align-items-center gap-3 mb-4">
                                <div class="bank-mini-logo">
                                    <div v-if="deposit.organization?.logo" class="rounded-circle bg-light border d-flex align-items-center justify-content-center overflow-hidden" style="width: 32px; height: 32px;">
                                        <img :src="deposit.organization.logo" :alt="deposit.organization.name" class="w-100 h-100 object-fit-contain">
                                    </div>
                                    <div v-else class="rounded-circle bg-light border d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="fa-solid fa-building-columns text-muted fs-6"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small fw-bold text-dark">{{ deposit.organization?.name || deposit.name }}</span>
                                        <span class="small fw-bold text-primary">{{ deposit.rate }}%</span>
                                    </div>
                                    <div class="progress" style="height: 6px; background: rgba(var(--bs-primary-rgb), 0.05);">
                                        <div class="progress-bar rounded-pill" 
                                             :style="`width: ${Math.min(deposit.rate * 5, 100)}%; background: var(--bs-primary);`" 
                                             role="progressbar"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-auto pt-4 d-flex align-items-center justify-content-between">
                            <button class="btn btn-light rounded-pill">{{ __('Open deposit') }}</button>
                            <p class="text-muted small mb-0 opacity-75 ps-3">
                                {{ __('Find the most profitable rate') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 3. Mortgage Rates -->
                <div class="col-lg-4">
                    <div class="monitoring-card h-100 bg-white p-3 p-xl-4 rounded-4 border-0 shadow-sm">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h3 class="fs-4 fw-bold mb-1">{{ __('Mortgage Rates') }}</h3>
                                <p class="text-muted small mb-0">{{ __('Checked rates in 59 banks') }}</p>
                            </div>
                            <div class="icon-circle bg-light text-dark">
                                <i class="fa-solid fa-house-chimney"></i>
                            </div>
                        </div>

                        <div class="mortgage-insurance-promo p-3 rounded-4 mb-4 d-flex align-items-center gap-3">
                             <div class="flex-grow-1">
                                 <h4 class="fs-6 fw-bold mb-1 text-dark">{{ __('Mortgage Insurance') }}</h4>
                                 <p class="text-muted small mb-0">{{ __('Save up to 40% on insurance cost') }}</p>
                             </div>
                             <div class="promo-image">
                                 <img src="/storage/images/icons/insurance-card.webp" alt="Promo">
                             </div>
                        </div>

                        <div class="mortgage-rates-list">
                            <div v-for="(type, index) in [__('Secondary housing'), __('New building'), __('Family'), __('Home construction')]" 
                                 :key="index" class="d-flex align-items-center gap-3 mb-4">
                                <span class="small fw-bold text-dark text-nowrap" style="min-width: 130px;">{{ type }}</span>
                                <div class="flex-grow-1">
                                    <div class="progress" style="height: 6px; background: rgba(var(--bs-primary-rgb), 0.05);">
                                        <div class="progress-bar rounded-pill" 
                                             :style="`width: ${[85, 75, 30, 90][index]}%; background: var(--bs-primary);`" 
                                             role="progressbar"></div>
                                    </div>
                                </div>
                                <span class="small fw-bold text-primary">{{ [16.9, 15.0, 5.9, 17.8][index] }}%</span>
                            </div>
                        </div>

                        <div class="mt-auto pt-4 d-flex align-items-center justify-content-between">
                            <button class="btn btn-light rounded-pill">{{ __('Find your rate') }}</button>
                            <p class="text-muted small mb-0 opacity-75 ps-3">
                                {{ __('... and calculate monthly payment') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>