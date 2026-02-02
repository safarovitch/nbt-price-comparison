<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import { logout } from '@/routes';
import { edit } from '@/routes/profile';
import type { User } from '@/types';
import { Link, router, usePage } from '@inertiajs/vue3';
import { LogOut, Settings } from 'lucide-vue-next';

interface Props {
    user: User;
}

const handleLogout = () => {
    router.flushAll();
};

defineProps<Props>();

const page = usePage();
const locale = page.props.locale as string || 'en';
</script>

<template>
    <div class="dropdown-menu show" style="position: static; display: block;">
        <h6 class="dropdown-header">
            <div class="d-flex align-items-center gap-2 px-1 py-2 text-start small">
                <UserInfo :user="user" :show-email="true" />
            </div>
        </h6>
        <div class="dropdown-divider"></div>
        <div>
            <Link class="dropdown-item" :href="edit(locale)" prefetch>
                <Settings class="me-2" style="width: 1rem; height: 1rem;" />
                Settings
            </Link>
        </div>
        <div class="dropdown-divider"></div>
        <Link class="dropdown-item" :href="logout()" @click="handleLogout" data-test="logout-button">
            <LogOut class="me-2" style="width: 1rem; height: 1rem;" />
            Log out
        </Link>
    </div>
</template>
