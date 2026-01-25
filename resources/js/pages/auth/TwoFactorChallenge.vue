<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { store } from '@/routes/two-factor/login';
import { Form, Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface AuthConfigContent {
    title: string;
    description: string;
    toggleText: string;
}

const authConfigContent = computed<AuthConfigContent>(() => {
    if (showRecoveryInput.value) {
        return {
            title: 'Recovery Code',
            description:
                'Please confirm access to your account by entering one of your emergency recovery codes.',
            toggleText: 'login using an authentication code',
        };
    }

    return {
        title: 'Authentication Code',
        description:
            'Enter the authentication code provided by your authenticator application.',
        toggleText: 'login using a recovery code',
    };
});

const showRecoveryInput = ref<boolean>(false);

const toggleRecoveryMode = (clearErrors: () => void): void => {
    showRecoveryInput.value = !showRecoveryInput.value;
    clearErrors();
    code.value = [];
};

const code = ref<number[]>([]);
const codeValue = computed<string>(() => code.value.join(''));
</script>

<template>
    <AuthLayout
        :title="authConfigContent.title"
        :description="authConfigContent.description"
    >
        <Head title="Two-Factor Authentication" />

        <div class="d-flex flex-column gap-3">
            <template v-if="!showRecoveryInput">
                <Form
                    v-bind="store.form()"
                    class="d-flex flex-column gap-3"
                    reset-on-error
                    @error="code = []"
                    #default="{ errors, processing, clearErrors }"
                >
                    <input type="hidden" name="code" :value="codeValue" />
                    <div
                        class="d-flex flex-column align-items-center justify-content-center gap-3 text-center"
                    >
                        <div class="d-flex w-100 align-items-center justify-content-center">
                            <input
                                id="otp"
                                type="text"
                                class="form-control text-center"
                                placeholder="000000"
                                maxlength="6"
                                pattern="[0-9]{6}"
                                v-model="codeValue"
                                :disabled="processing"
                                autofocus
                                style="letter-spacing: 0.5em; font-size: 1.5rem; width: 200px;"
                            />
                        </div>
                        <InputError :message="errors.code" />
                    </div>
                    <button type="submit" class="btn btn-primary w-100" :disabled="processing">
                        Continue
                    </button>
                    <div class="text-center text-muted small">
                        <span>or you can </span>
                        <button
                            type="button"
                            class="btn btn-link p-0 text-decoration-underline"
                            @click="() => toggleRecoveryMode(clearErrors)"
                        >
                            {{ authConfigContent.toggleText }}
                        </button>
                    </div>
                </Form>
            </template>

            <template v-else>
                <Form
                    v-bind="store.form()"
                    class="d-flex flex-column gap-3"
                    reset-on-error
                    #default="{ errors, processing, clearErrors }"
                >
                    <input
                        name="recovery_code"
                        type="text"
                        class="form-control"
                        placeholder="Enter recovery code"
                        :autofocus="showRecoveryInput"
                        required
                    />
                    <InputError :message="errors.recovery_code" />
                    <button type="submit" class="btn btn-primary w-100" :disabled="processing">
                        Continue
                    </button>

                    <div class="text-center text-muted small">
                        <span>or you can </span>
                        <button
                            type="button"
                            class="btn btn-link p-0 text-decoration-underline"
                            @click="() => toggleRecoveryMode(clearErrors)"
                        >
                            {{ authConfigContent.toggleText }}
                        </button>
                    </div>
                </Form>
            </template>
        </div>
    </AuthLayout>
</template>
