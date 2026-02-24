<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { dashboard } from '@/routes';
import admin from '@/routes/admin';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Building2, Folder, LayoutGrid, Menu, MessageSquare, Newspaper, X } from 'lucide-vue-next';
import { ref } from 'vue';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Organizations',
        href: admin.organizations.index().url,
        icon: Building2,
    },
    {
        title: 'News',
        href: '/admin/news',
        icon: Newspaper,
    },
    {
        title: 'Comments',
        href: '/admin/comments',
        icon: MessageSquare,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/safarovitch/nbt-price-comparison',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];

const showMobileSidebar = ref(false);
</script>

<template>
    <!-- Mobile Toggle Button -->
    <button class="btn btn-dark d-md-none position-fixed top-0 start-0 m-3" style="z-index: 1040;" @click="showMobileSidebar = true" type="button">
        <Menu style="width: 1.25rem; height: 1.25rem;" />
    </button>

    <!-- Desktop Sidebar (Fixed) -->
    <aside class="d-none d-md-flex flex-column bg-dark text-white position-fixed top-0 start-0 vh-100 border-end" style="width: 16rem; z-index: 1030;">
        <div class="p-3 border-bottom border-secondary">
            <Link :href="dashboard()" class="text-decoration-none">
                <AppLogo />
            </Link>
        </div>

        <div class="flex-grow-1 p-3 overflow-auto">
            <NavMain :items="mainNavItems" />
        </div>

        <div class="p-3 border-top border-secondary">
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </div>
    </aside>

    <!-- Mobile Offcanvas Sidebar -->
    <div class="offcanvas offcanvas-start bg-dark text-white d-md-none" :class="{ show: showMobileSidebar }" tabindex="-1" style="width: 16rem;">
        <div class="offcanvas-header border-bottom border-secondary">
            <Link :href="dashboard()" class="text-decoration-none" @click="showMobileSidebar = false">
                <AppLogo />
            </Link>
            <button type="button" class="btn-close btn-close-white" @click="showMobileSidebar = false" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body d-flex flex-column p-0">
            <div class="flex-grow-1 p-3 overflow-auto">
                <NavMain :items="mainNavItems" @click="showMobileSidebar = false" />
            </div>

            <div class="p-3 border-top border-secondary">
                <NavFooter :items="footerNavItems" />
                <NavUser />
            </div>
        </div>
    </div>

    <!-- Backdrop for mobile offcanvas -->
    <div v-if="showMobileSidebar" class="offcanvas-backdrop fade show d-md-none" @click="showMobileSidebar = false"></div>
</template>

<style scoped>
.offcanvas {
    visibility: hidden;
    transform: translateX(-100%);
    transition: transform 0.3s ease-in-out;
}

.offcanvas.show {
    visibility: visible;
    transform: translateX(0);
}
</style>
