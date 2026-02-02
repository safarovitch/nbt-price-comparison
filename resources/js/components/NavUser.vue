<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import { usePage } from '@inertiajs/vue3';
import { ChevronsUpDown } from 'lucide-vue-next';
import UserMenuContent from './UserMenuContent.vue';
import { ref } from 'vue';

const page = usePage();
const user = page.props.auth.user;
const showDropdown = ref(false);
</script>

<template>
    <div class="dropdown">
        <button type="button" class="btn btn-link w-100 text-start text-decoration-none text-white d-flex align-items-center justify-content-between p-2" data-bs-toggle="dropdown" data-test="sidebar-menu-button" @click="showDropdown = !showDropdown">
            <UserInfo :user="user" />
            <ChevronsUpDown style="width: 1rem; height: 1rem;" />
        </button>
        <div class="dropdown-menu dropdown-menu-end w-100">
            <UserMenuContent :user="user" />
        </div>
    </div>
</template>
