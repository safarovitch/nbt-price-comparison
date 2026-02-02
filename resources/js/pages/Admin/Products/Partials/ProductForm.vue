<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TranslatableInput from '@/components/TranslatableInput.vue';
import { useForm } from '@inertiajs/vue3';
import { Save } from 'lucide-vue-next';

interface TranslatableValue {
    ru?: string | null;
    en?: string | null;
    tj?: string | null;
}

const props = defineProps<{
    product?: any;
    submitLabel: string;
    onSubmit: (form: any) => void;
}>();

// Helper to normalize translatable field from product prop
const getTranslatable = (field: string): TranslatableValue => {
    const value = props.product?.[field];
    if (!value) return { ru: '', en: '', tj: '' };
    if (typeof value === 'string') return { ru: value, en: '', tj: '' };
    return {
        ru: value.ru ?? '',
        en: value.en ?? '',
        tj: value.tj ?? '',
    };
};

const form = useForm({
    name: getTranslatable('name'),
    type: props.product?.type ?? 'credit',
    currency_code: props.product?.currency_code ?? 972, // TJS
    interest_rate: props.product?.interest_rate ?? '',
    fees: props.product?.fees ?? '',
    term_months: props.product?.term_months ?? '',
    min_amount: props.product?.min_amount ?? '',
    max_amount: props.product?.max_amount ?? '',
    eligibility: getTranslatable('eligibility'),
    description: getTranslatable('description'),
    extra: props.product?.extra ?? {},
});

const submit = () => {
    props.onSubmit(form);
};
</script>

<template>
    <form @submit.prevent="submit" class="row g-3">
        <!-- Translatable Product Name -->
        <div class="col-12">
            <TranslatableInput v-model="form.name" label="Product Name" placeholder="e.g. Consumer Loan" :required="true" :error="form.errors.name" />
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Type</label>
            <select v-model="form.type" class="form-select">
                <option value="credit">Credit</option>
                <option value="deposit">Deposit</option>
                <option value="insurance">Insurance</option>
                <option value="transfer">Transfer</option>
                <option value="mortgage">Mortgage</option>
                <option value="card">Card</option>
                <option value="islamic">Islamic</option>
                <option value="other">Other</option>
            </select>
            <InputError :message="form.errors.type" />
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Currency</label>
            <select v-model="form.currency_code" class="form-select">
                <option :value="972">TJS (Somoni)</option>
                <option :value="840">USD (Dollar)</option>
                <option :value="978">EUR (Euro)</option>
                <option :value="643">RUB (Ruble)</option>
            </select>
            <InputError :message="form.errors.currency_code" />
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Interest Rate (%)</label>
            <input v-model="form.interest_rate" type="number" step="0.01" class="form-control" placeholder="12.5" />
            <InputError :message="form.errors.interest_rate" />
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Fees</label>
            <input v-model="form.fees" type="number" step="0.01" class="form-control" placeholder="0.00" />
            <InputError :message="form.errors.fees" />
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Term (Months)</label>
            <input v-model="form.term_months" type="number" class="form-control" placeholder="12" />
            <InputError :message="form.errors.term_months" />
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Min Amount</label>
            <input v-model="form.min_amount" type="number" step="0.01" class="form-control" placeholder="1000" />
            <InputError :message="form.errors.min_amount" />
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Max Amount</label>
            <input v-model="form.max_amount" type="number" step="0.01" class="form-control" placeholder="50000" />
            <InputError :message="form.errors.max_amount" />
        </div>

        <!-- Translatable Eligibility -->
        <div class="col-12">
            <TranslatableInput v-model="form.eligibility" label="Eligibility" type="textarea" placeholder="Requirements for this product..." :rows="2" :error="form.errors.eligibility" />
        </div>

        <!-- Translatable Description -->
        <div class="col-12">
            <TranslatableInput v-model="form.description" label="Description" type="textarea" placeholder="Full product description..." :rows="3" :error="form.errors.description" />
        </div>

        <div class="col-12 text-end mt-4">
            <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2" :disabled="form.processing">
                <span v-if="form.processing" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <Save v-else style="width: 1.25rem; height: 1.25rem;" />
                {{ submitLabel }}
            </button>
        </div>
    </form>
</template>
