<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { logout } from '@/routes';
import { send } from '@/routes/verification';
import { Form, Head } from '@inertiajs/vue3';

defineProps<{
    status?: string;
}>();
</script>

<template>
    <AuthLayout
        title="Verify email"
        description="Please verify your email address by clicking on the link we just emailed to you."
    >
        <Head title="Email verification" />

        <div
            v-if="status === 'verification-link-sent'"
            class="mb-3 text-center small fw-medium text-success"
        >
            A new verification link has been sent to the email address you
            provided during registration.
        </div>

        <Form
            v-bind="send.form()"
            class="d-flex flex-column gap-3 text-center"
            v-slot="{ processing }"
        >
            <button type="submit" class="btn btn-secondary" :disabled="processing">
                <span
                    v-if="processing"
                    class="spinner-border spinner-border-sm me-2"
                    role="status"
                    aria-hidden="true"
                ></span>
                Resend verification email
            </button>

            <TextLink
                :href="logout()"
                as="button"
                class="mx-auto d-block small"
            >
                Log out
            </TextLink>
        </Form>
    </AuthLayout>
</template>
