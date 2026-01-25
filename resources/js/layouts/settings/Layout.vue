<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { toUrl, urlIsActive } from '@/lib/utils';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editProfile } from '@/routes/profile';
import { show } from '@/routes/two-factor';
import { edit as editPassword } from '@/routes/user-password';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';

const sidebarNavItems: NavItem[] = [
    {
        title: 'Profile',
        href: editProfile(),
    },
    {
        title: 'Password',
        href: editPassword(),
    },
    {
        title: 'Two-Factor Auth',
        href: show(),
    },
    {
        title: 'Appearance',
        href: editAppearance(),
    },
];

const currentPath = typeof window !== undefined ? window.location.pathname : '';
</script>

<template>
    <div class="px-4 py-6">
        <Heading
            title="Settings"
            description="Manage your profile and account settings"
        />

        <div class="d-flex flex-column flex-lg-row gap-4">
            <aside class="w-100" style="max-width: 12rem;">
                <nav class="nav flex-column gap-1">
                    <Link
                        v-for="item in sidebarNavItems"
                        :key="toUrl(item.href)"
                        :href="item.href"
                        :class="[
                            'nav-link',
                            { 'active': urlIsActive(item.href, currentPath) },
                        ]"
                    >
                        <component v-if="item.icon" :is="item.icon" class="me-2" style="width: 1rem; height: 1rem;" />
                        {{ item.title }}
                    </Link>
                </nav>
            </aside>

            <hr class="my-4 d-lg-none" />

            <div class="flex-grow-1" style="max-width: 42rem;">
                <section class="d-flex flex-column gap-5" style="max-width: 36rem;">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
