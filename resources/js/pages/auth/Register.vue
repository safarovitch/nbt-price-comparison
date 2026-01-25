<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { store } from '@/routes/register';
import { Form, Head } from '@inertiajs/vue3';
</script>

<template>
    <AuthBase
        title="Create an account"
        description="Enter your details below to create your account"
    >
        <Head title="Register" />

        <Form
            v-bind="store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="d-flex flex-column gap-3"
        >
            <div class="d-flex flex-column gap-3">
                <div class="d-flex flex-column gap-2">
                    <label for="name" class="form-label">Name</label>
                    <input
                        id="name"
                        type="text"
                        class="form-control"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        name="name"
                        placeholder="Full name"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="d-flex flex-column gap-2">
                    <label for="email" class="form-label">Email address</label>
                    <input
                        id="email"
                        type="email"
                        class="form-control"
                        required
                        :tabindex="2"
                        autocomplete="email"
                        name="email"
                        placeholder="email@example.com"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="d-flex flex-column gap-2">
                    <label for="password" class="form-label">Password</label>
                    <input
                        id="password"
                        type="password"
                        class="form-control"
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        name="password"
                        placeholder="Password"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="d-flex flex-column gap-2">
                    <label for="password_confirmation" class="form-label">Confirm password</label>
                    <input
                        id="password_confirmation"
                        type="password"
                        class="form-control"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        name="password_confirmation"
                        placeholder="Confirm password"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <button
                    type="submit"
                    class="btn btn-primary mt-2 w-100"
                    tabindex="5"
                    :disabled="processing"
                    data-test="register-user-button"
                >
                    <span
                        v-if="processing"
                        class="spinner-border spinner-border-sm me-2"
                        role="status"
                        aria-hidden="true"
                    ></span>
                    Create account
                </button>
            </div>

            <div class="text-center text-muted small">
                Already have an account?
                <TextLink
                    :href="login()"
                    class="text-decoration-underline"
                    :tabindex="6"
                    >Log in</TextLink
                >
            </div>
        </Form>
    </AuthBase>
</template>
