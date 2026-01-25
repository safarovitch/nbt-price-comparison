<script setup lang="ts">
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { Form } from '@inertiajs/vue3';
import { onMounted, ref, useTemplateRef } from 'vue';

// Components
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';

const passwordInput = useTemplateRef('passwordInput');
const modalId = 'deleteUserModal';
const modalElement = ref<HTMLElement | null>(null);

onMounted(() => {
    modalElement.value = document.getElementById(modalId);
});
</script>

<template>
    <div class="d-flex flex-column gap-4">
        <HeadingSmall
            title="Delete account"
            description="Delete your account and all of its resources"
        />
        <div
            class="d-flex flex-column gap-3 rounded border border-danger border-opacity-25 bg-danger bg-opacity-10 p-3"
        >
            <div class="d-flex flex-column gap-1 text-danger">
                <p class="fw-medium mb-0">Warning</p>
                <p class="small mb-0">
                    Please proceed with caution, this cannot be undone.
                </p>
            </div>
            <button
                type="button"
                class="btn btn-danger"
                data-bs-toggle="modal"
                :data-bs-target="`#${modalId}`"
                data-test="delete-user-button"
            >
                Delete account
            </button>
        </div>

        <!-- Bootstrap Modal -->
        <div
            :id="modalId"
            class="modal fade"
            tabindex="-1"
            aria-labelledby="deleteUserModalLabel"
            aria-hidden="true"
            ref="modalElement"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <Form
                        v-bind="ProfileController.destroy.form()"
                        reset-on-success
                        @error="() => passwordInput?.focus()"
                        :options="{
                            preserveScroll: true,
                        }"
                        class="d-flex flex-column gap-3"
                        v-slot="{ errors, processing, reset, clearErrors }"
                    >
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteUserModalLabel">
                                Are you sure you want to delete your account?
                            </h5>
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                                @click="
                                    () => {
                                        clearErrors();
                                        reset();
                                    }
                                "
                            ></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted">
                                Once your account is deleted, all of its
                                resources and data will also be permanently
                                deleted. Please enter your password to confirm
                                you would like to permanently delete your
                                account.
                            </p>

                            <div class="d-flex flex-column gap-2">
                                <label for="password" class="visually-hidden">Password</label>
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    ref="passwordInput"
                                    class="form-control"
                                    placeholder="Password"
                                />
                                <InputError :message="errors.password" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal"
                                @click="
                                    () => {
                                        clearErrors();
                                        reset();
                                    }
                                "
                            >
                                Cancel
                            </button>

                            <button
                                type="submit"
                                class="btn btn-danger"
                                :disabled="processing"
                                data-test="confirm-delete-user-button"
                            >
                                Delete account
                            </button>
                        </div>
                    </Form>
                </div>
            </div>
        </div>
    </div>
</template>
