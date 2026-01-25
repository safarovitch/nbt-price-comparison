<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { email } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';

defineProps<{
    status?: string;
}>();
</script>

<template>
    <AuthLayout
        title="Forgot password"
        description="Enter your email to receive a password reset link"
    >
        <Head title="Forgot password" />

        <div
            v-if="status"
            class="mb-3 text-center small fw-medium text-success"
        >
            {{ status }}
        </div>

        <div class="d-flex flex-column gap-4">
            <Form v-bind="email.form()" v-slot="{ errors, processing }">
                <div class="d-flex flex-column gap-2">
                    <label for="email" class="form-label">Email address</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        class="form-control"
                        autocomplete="off"
                        autofocus
                        placeholder="email@example.com"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="my-4 d-flex align-items-center justify-content-start">
                    <button
                        type="submit"
                        class="btn btn-primary w-100"
                        :disabled="processing"
                        data-test="email-password-reset-link-button"
                    >
                        <span
                            v-if="processing"
                            class="spinner-border spinner-border-sm me-2"
                            role="status"
                            aria-hidden="true"
                        ></span>
                        Email password reset link
                    </button>
                </div>
            </Form>

            <div class="text-center text-muted small">
                <span>Or, return to</span>
                <TextLink :href="login()">log in</TextLink>
            </div>
        </div>
    </AuthLayout>
</template>
