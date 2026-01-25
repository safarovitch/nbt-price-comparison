<script setup lang="ts">
import { useInitials } from '@/composables/useInitials';
import type { User } from '@/types';
import { computed } from 'vue';

interface Props {
    user: User;
    showEmail?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    showEmail: false,
});

const { getInitials } = useInitials();

// Compute whether we should show the avatar image
const showAvatar = computed(
    () => props.user.avatar && props.user.avatar !== '',
);
</script>

<template>
    <div class="d-flex align-items-center">
        <div class="flex-shrink-0">
            <img
                v-if="showAvatar"
                :src="user.avatar!"
                :alt="user.name"
                class="rounded"
                style="width: 2rem; height: 2rem; object-fit: cover;"
            />
            <div
                v-else
                class="rounded d-flex align-items-center justify-content-center bg-secondary text-dark"
                style="width: 2rem; height: 2rem;"
            >
                {{ getInitials(user.name) }}
            </div>
        </div>

        <div class="flex-grow-1 ms-2 text-start">
            <div class="fw-medium text-truncate small">{{ user.name }}</div>
            <div v-if="showEmail" class="text-muted text-truncate" style="font-size: 0.75rem;">
                {{ user.email }}
            </div>
        </div>
    </div>
</template>
