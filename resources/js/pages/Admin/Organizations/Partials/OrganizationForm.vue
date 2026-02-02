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
    organization?: any;
    submitLabel: string;
    onSubmit: (form: any) => void;
}>();

// Helper to normalize translatable field from organization prop
const getTranslatable = (field: string): TranslatableValue => {
    const value = props.organization?.[field];
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
    type: props.organization?.type ?? 'bank',
    category: props.organization?.category ?? 'finance',
    status: props.organization?.status ?? 'active',
    base_url: props.organization?.base_url ?? '',
    auth_type: props.organization?.auth_type ?? 'api_key',
    auth_value: props.organization?.auth_value ?? '',
    api_key: props.organization?.api_key ?? '',
    webhook_secret: props.organization?.webhook_secret ?? '',
    description: getTranslatable('description'),
    legal_address: getTranslatable('legal_address'),
    registration_number: props.organization?.registration_number ?? '',
    website: props.organization?.website ?? '',
    tin: props.organization?.tin ?? '',
    emails: props.organization?.emails ?? '',
    phones: props.organization?.phones ?? '',
    mobile_apps: props.organization?.mobile_apps ?? '',
    social_media: props.organization?.social_media ?? '',
    sync_type: props.organization?.sync_type ?? 'manual',
    endpoints: props.organization?.endpoints ? JSON.stringify(props.organization.endpoints, null, 2) : '',
});

const submit = () => {
    props.onSubmit(form);
};
</script>

<template>
    <form @submit.prevent="submit" class="row g-3">
        <!-- Translatable Name -->
        <div class="col-12">
            <TranslatableInput v-model="form.name" label="Organization Name" placeholder="e.g. National Bank" :required="true" :error="form.errors.name" />
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Type</label>
            <select v-model="form.type" class="form-select">
                <option value="bank">Bank</option>
                <option value="mfo">MFO</option>
                <option value="insurance">Insurance</option>
                <option value="other">Other</option>
            </select>
            <InputError :message="form.errors.type" />
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Category</label>
            <select v-model="form.category" class="form-select">
                <option value="finance">Finance</option>
                <option value="insurance">Insurance</option>
                <option value="mfo">MFO</option>
                <option value="other">Other</option>
            </select>
            <InputError :message="form.errors.category" />
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Status</label>
            <select v-model="form.status" class="form-select">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            <InputError :message="form.errors.status" />
        </div>

        <div class="col-12">
            <label class="form-label fw-semibold">Base URL (API)</label>
            <input v-model="form.base_url" type="url" class="form-control" placeholder="https://api.example.com/v1" />
            <InputError :message="form.errors.base_url" />
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Auth Type</label>
            <select v-model="form.auth_type" class="form-select">
                <option value="api_key">API Key (X-API-Key)</option>
                <option value="bearer">Bearer Token</option>
                <option value="sha256_signature">SHA256 Signature (X-Key/X-Sign)</option>
                <option value="none">None</option>
            </select>
            <InputError :message="form.errors.auth_type" />
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Auth Value</label>
            <input v-model="form.auth_value" type="text" class="form-control" placeholder="Secret Token or key:secret" />
            <InputError :message="form.errors.auth_value" />
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Webhook Secret</label>
            <input v-model="form.webhook_secret" type="text" class="form-control" placeholder="For verifying inbound webhooks" />
            <InputError :message="form.errors.webhook_secret" />
        </div>

        <!-- Translatable Description -->
        <div class="col-12">
            <TranslatableInput v-model="form.description" label="Description" type="textarea" placeholder="Organization description" :rows="3" :error="form.errors.description" />
        </div>

        <!-- Translatable Legal Address -->
        <div class="col-12">
            <TranslatableInput v-model="form.legal_address" label="Legal Address" type="textarea" placeholder="Legal address" :rows="2" :error="form.errors.legal_address" />
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Website</label>
            <input v-model="form.website" type="url" class="form-control" placeholder="https://www.example.com" />
            <InputError :message="form.errors.website" />
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Registration Number</label>
            <input v-model="form.registration_number" type="text" class="form-control" />
            <InputError :message="form.errors.registration_number" />
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">TIN</label>
            <input v-model="form.tin" type="text" class="form-control" />
            <InputError :message="form.errors.tin" />
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">API Key</label>
            <input v-model="form.api_key" type="text" class="form-control" placeholder="Legacy API Key field" />
            <InputError :message="form.errors.api_key" />
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Sync Type</label>
            <select v-model="form.sync_type" class="form-select">
                <option value="manual">Manual</option>
                <option value="auto">Auto</option>
                <option value="scheduled">Scheduled</option>
            </select>
            <InputError :message="form.errors.sync_type" />
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Emails</label>
            <input v-model="form.emails" type="text" class="form-control" placeholder="Comma-separated emails" />
            <InputError :message="form.errors.emails" />
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Phones</label>
            <input v-model="form.phones" type="text" class="form-control" placeholder="Comma-separated phone numbers" />
            <InputError :message="form.errors.phones" />
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Mobile Apps</label>
            <input v-model="form.mobile_apps" type="text" class="form-control" placeholder="App store links (JSON or comma-separated)" />
            <InputError :message="form.errors.mobile_apps" />
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Social Media</label>
            <input v-model="form.social_media" type="text" class="form-control" placeholder="Social media links (JSON or comma-separated)" />
            <InputError :message="form.errors.social_media" />
        </div>

        <div class="col-12">
            <label class="form-label fw-semibold">Endpoints (JSON)</label>
            <textarea v-model="form.endpoints" class="form-control font-monospace" rows="3" placeholder='{"products": "/api/products", "applications": "/api/applications"}'></textarea>
            <InputError :message="form.errors.endpoints" />
            <small class="text-muted">Enter API endpoints as JSON object</small>
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
