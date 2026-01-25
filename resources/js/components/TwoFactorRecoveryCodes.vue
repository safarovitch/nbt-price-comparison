<script setup lang="ts">
import AlertError from '@/components/AlertError.vue';
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';
import { regenerateRecoveryCodes } from '@/routes/two-factor';
import { Form } from '@inertiajs/vue3';
import { Eye, EyeOff, LockKeyhole, RefreshCw } from 'lucide-vue-next';
import { nextTick, onMounted, ref, useTemplateRef } from 'vue';

const { recoveryCodesList, fetchRecoveryCodes, errors } = useTwoFactorAuth();
const isRecoveryCodesVisible = ref<boolean>(false);
const recoveryCodeSectionRef = useTemplateRef('recoveryCodeSectionRef');

const toggleRecoveryCodesVisibility = async () => {
    if (!isRecoveryCodesVisible.value && !recoveryCodesList.value.length) {
        await fetchRecoveryCodes();
    }

    isRecoveryCodesVisible.value = !isRecoveryCodesVisible.value;

    if (isRecoveryCodesVisible.value) {
        await nextTick();
        recoveryCodeSectionRef.value?.scrollIntoView({ behavior: 'smooth' });
    }
};

onMounted(async () => {
    if (!recoveryCodesList.value.length) {
        await fetchRecoveryCodes();
    }
});
</script>

<template>
    <div class="card w-100">
        <div class="card-header">
            <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                <LockKeyhole style="width: 1rem; height: 1rem;" />
                2FA Recovery Codes
            </h5>
            <p class="card-text text-muted small mb-0 mt-2">
                Recovery codes let you regain access if you lose your 2FA
                device. Store them in a secure password manager.
            </p>
        </div>
        <div class="card-body">
            <div
                class="d-flex flex-column gap-3 user-select-none flex-sm-row align-items-sm-center justify-content-sm-between"
            >
                <button
                    type="button"
                    class="btn btn-primary"
                    @click="toggleRecoveryCodesVisibility"
                    style="width: fit-content;"
                >
                    <component
                        :is="isRecoveryCodesVisible ? EyeOff : Eye"
                        style="width: 1rem; height: 1rem;"
                        class="me-1"
                    />
                    {{ isRecoveryCodesVisible ? 'Hide' : 'View' }} Recovery
                    Codes
                </button>

                <Form
                    v-if="isRecoveryCodesVisible && recoveryCodesList.length"
                    v-bind="regenerateRecoveryCodes.form()"
                    method="post"
                    :options="{ preserveScroll: true }"
                    @success="fetchRecoveryCodes"
                    #default="{ processing }"
                >
                    <button
                        type="submit"
                        class="btn btn-secondary"
                        :disabled="processing"
                    >
                        <RefreshCw style="width: 1rem; height: 1rem;" class="me-1" />
                        Regenerate Codes
                    </button>
                </Form>
            </div>
            <div
                :class="[
                    'position-relative overflow-hidden transition-all',
                    isRecoveryCodesVisible
                        ? 'h-auto opacity-100'
                        : 'h-0 opacity-0',
                ]"
                style="transition-duration: 300ms;"
            >
                <div v-if="errors?.length" class="mt-4">
                    <AlertError :errors="errors" />
                </div>
                <div v-else class="mt-3 d-flex flex-column gap-3">
                    <div
                        ref="recoveryCodeSectionRef"
                        class="d-grid gap-1 rounded bg-light p-3 font-monospace small"
                    >
                        <div v-if="!recoveryCodesList.length" class="d-flex flex-column gap-2">
                            <div
                                v-for="n in 8"
                                :key="n"
                                class="bg-secondary bg-opacity-20 rounded"
                                style="height: 1rem; animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;"
                            ></div>
                        </div>
                        <div
                            v-else
                            v-for="(code, index) in recoveryCodesList"
                            :key="index"
                        >
                            {{ code }}
                        </div>
                    </div>
                    <p class="text-muted small user-select-none">
                        Each recovery code can be used once to access your
                        account and will be removed after use. If you need more,
                        click
                        <span class="fw-bold">Regenerate Codes</span> above.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
