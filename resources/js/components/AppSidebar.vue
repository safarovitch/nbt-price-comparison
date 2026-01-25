<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <aside class="border-end bg-light" style="width: 16rem; min-height: 100vh;">
        <div class="d-flex flex-column h-100">
            <div class="p-3 border-bottom">
                <Link :href="dashboard()" class="text-decoration-none">
                    <AppLogo />
                </Link>
            </div>

            <div class="flex-grow-1 p-3">
                <NavMain :items="mainNavItems" />
            </div>

            <div class="p-3 border-top">
                <NavFooter :items="footerNavItems" />
                <NavUser />
            </div>
        </div>
    </aside>
    <slot />
</template>
