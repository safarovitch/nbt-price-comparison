<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import AuthBase from '@/layouts/AuthLayout.vue';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();
</script>

<template>
    <AuthBase
        title="Log in to your account"
        description="Enter your email and password below to log in"
    >
        <Head title="Log in" />

        <div
            v-if="status"
            class="mb-3 text-center small fw-medium text-success"
        >
            {{ status }}
        </div>

        <Form
            v-bind="store.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="d-flex flex-column gap-3"
        >
            <div class="d-flex flex-column gap-3">
                <div class="d-flex flex-column gap-2">
                    <label for="email" class="form-label">Email address</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        class="form-control"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        placeholder="email@example.com"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="d-flex flex-column gap-2">
                    <div class="d-flex align-items-center justify-content-between">
                        <label for="password" class="form-label">Password</label>
                        <TextLink
                            v-if="canResetPassword"
                            :href="request()"
                            class="text-decoration-none small"
                            :tabindex="5"
                        >
                            Forgot password?
                        </TextLink>
                    </div>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="form-control"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        placeholder="Password"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="form-check">
                    <input
                        id="remember"
                        name="remember"
                        class="form-check-input"
                        type="checkbox"
                        :tabindex="3"
                    />
                    <label class="form-check-label" for="remember">
                        Remember me
                    </label>
                </div>

                <button
                    type="submit"
                    class="btn btn-primary mt-2 w-100"
                    :tabindex="4"
                    :disabled="processing"
                    data-test="login-button"
                >
                    <span
                        v-if="processing"
                        class="spinner-border spinner-border-sm me-2"
                        role="status"
                        aria-hidden="true"
                    ></span>
                    Log in
                </button>
            </div>

            <div class="text-center text-muted small" v-if="canRegister">
                Don't have an account?
                <TextLink :href="register()" :tabindex="5">Sign up</TextLink>
            </div>
        </Form>
    </AuthBase>
</template>
