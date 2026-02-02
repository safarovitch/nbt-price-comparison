<script setup lang="ts">
import GuestLayout from '@/layouts/GuestLayout.vue';
import InsuranceHero from '@/components/Insurance/InsuranceHero.vue';
import InsuranceCategories from '@/components/Insurance/InsuranceCategories.vue';
import InsuranceBenefits from '@/components/Insurance/InsuranceBenefits.vue';
import InsurancePartners from '@/components/Insurance/InsurancePartners.vue';
import InsuranceReviews from '@/components/Insurance/InsuranceReviews.vue';
import InsuranceFaq from '@/components/Insurance/InsuranceFaq.vue';
import InsuranceSteps from '@/components/Insurance/InsuranceSteps.vue';
import { Head } from '@inertiajs/vue3';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();

defineProps<{
    partners: any[];
}>();
</script>

<template>

    <Head :title="__('Insurance Comparison')" />

    <GuestLayout>
        <!-- Hero Search / Title -->
        <InsuranceHero />

        <!-- Main Category Grid -->
        <InsuranceCategories />

        <!-- Benefits Summary -->
        <InsuranceBenefits />

        <!-- Insurance Partners / Merchants -->
        <InsurancePartners :partners="partners" />

        <!-- Reviews & Ratings -->
        <InsuranceReviews />

        <!-- Steps to follow in accident -->
        <InsuranceSteps />

        <!-- FAQ Section -->
        <InsuranceFaq />
    </GuestLayout>
</template>

<style scoped>
/* Page specific styles if any */
</style>
