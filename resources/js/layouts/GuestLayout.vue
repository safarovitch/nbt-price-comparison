<script setup lang="ts">
import { onMounted, ref, nextTick, computed, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import { index, login, register, insurance } from '@/routes';
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';
import SecurityFooter from '@/components/SecurityFooter.vue';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();

const preloaderVisible = ref(true);
const activeMenuIndex = ref<number | null>(null);
const lastActiveIndex = ref<number | null>(null); // Store last active index for fade-out

const isMenuOpen = computed(() => activeMenuIndex.value !== null);
const displayIndex = computed(() => activeMenuIndex.value !== null ? activeMenuIndex.value : lastActiveIndex.value);

let closeTimer: ReturnType<typeof setTimeout> | null = null;

// Watch for active index changes to update key for transitions or tracking
watch(activeMenuIndex, (newVal) => {
    if (newVal !== null) {
        lastActiveIndex.value = newVal;
    }
});

const menuItems = computed(() => [
    {
        label: __('Credits'),
        link: '#',
        items: [
            { name: __('Consumer Loans'), icon: 'fa-solid fa-hand-holding-dollar' },
            { name: __('Car Loans'), icon: 'fa-solid fa-car' },
            { name: __('Microloans'), icon: 'fa-solid fa-coins' },
            { name: __('Murabaha'), icon: 'fa-regular fa-handshake' },
            { name: __('Mudaraba'), icon: 'fa-solid fa-users-viewfinder' },
            { name: __('Musharaka'), icon: 'fa-solid fa-briefcase' },
        ]
    },
    {
        label: __('Deposits'),
        link: '#',
        items: [
            { name: __('Deposits'), icon: 'fa-solid fa-vault' },
            // { name: __('Deposits'), icon: 'fa-solid fa-sack-dollar' }, // Deduplicated in EN translation map keys if needed
        ]
    },
    {
        label: __('Mortgage'),
        link: '#',
        items: [
            { name: __('Mortgage Programs'), icon: 'fa-solid fa-house-chimney' },
            { name: __('Islamic Mortgage'), icon: 'fa-solid fa-mosque' },
        ]
    },
    {
        label: __('Cards'),
        link: '#',
        items: [
            { name: __('Credit Cards'), icon: 'fa-regular fa-credit-card' },
            { name: __('Islamic Credit Cards'), icon: 'fa-regular fa-credit-card' },
            { name: __('Debit Cards'), icon: 'fa-regular fa-id-card' },
            { name: __('E-wallets'), icon: 'fa-solid fa-wallet' },
        ]
    },

    {
        label: __('Business'),
        link: '#',
        items: [
            { name: __('Current Accounts'), icon: 'fa-regular fa-file-lines' },
            { name: __('Cash Settlement'), icon: 'fa-solid fa-cash-register' },
        ]
    },
    {
        label: __('Insurance'),
        link: '#',
        items: [
            { name: __('OSAGO'), icon: 'fa-solid fa-car-burst', link : insurance.url() },
            { name: __('Life Insurance'), icon: 'fa-solid fa-user-shield' },
            { name: __('Medical Insurance'), icon: 'fa-solid fa-heart-pulse' },
            { name: __('Car Insurance'), icon: 'fa-solid fa-car-side' },
        ]
    },
    {
        label: __('Transfers'),
        link: '#',
        items: [
            { name: __('Money Transfers'), icon: 'fa-solid fa-money-bill-transfer' },
        ]
    },
    {
        label: __('Migrants'),
        link: '#',
        items: [
            { name: __('Migrant Services'), icon: 'fa-solid fa-passport' },
        ]
    }
]);

const handleMouseEnter = (index: number) => {
    if (closeTimer) {
        clearTimeout(closeTimer);
        closeTimer = null;
    }
    activeMenuIndex.value = index;
};

const handleMouseLeave = () => {
    closeTimer = setTimeout(() => {
        activeMenuIndex.value = null;
        // Note: lastActiveIndex retains the value for rendering during fade out
    }, 200); // 200ms delay to allow moving to content
};

const handleContentMouseEnter = () => {
    if (closeTimer) {
        clearTimeout(closeTimer);
        closeTimer = null;
    }
};

onMounted(async () => {
    // Hide preloader function
    const hidePreloader = () => {
        const preloader = document.querySelector('.preloader');
        if (preloader) {
            preloaderVisible.value = false;
            if (window.$ && typeof window.$.fn.fadeOut === 'function') {
                window.$(preloader).fadeOut(600, () => {
                    (preloader as HTMLElement).style.display = 'none';
                });
            } else {
                (preloader as HTMLElement).style.opacity = '0';
                (preloader as HTMLElement).style.transition = 'opacity 0.6s';
                setTimeout(() => {
                    (preloader as HTMLElement).style.display = 'none';
                }, 600);
            }
        }
    };

    setTimeout(hidePreloader, 500);

    try {
        // Load theme JS files
        await Promise.all([
            import('../theme/jquery.slicknav.js'),
            import('../theme/swiper-bundle.min.js'),
            import('../theme/jquery.waypoints.min.js'),
            import('../theme/jquery.counterup.min.js'),
            import('../theme/jquery.magnific-popup.min.js'),
            import('../theme/gsap.min.js'),
            import('../theme/ScrollTrigger.min.js'),
            import('../theme/SplitText.js'),
            import('../theme/wow.js')
        ]);

        await nextTick();
        await import('../theme/function.js');

        await nextTick();
        if (typeof (window as any).WOW !== 'undefined') {
            try {
                new (window as any).WOW().init();
            } catch (e) { }
        }

        if (document.readyState === 'complete') {
            hidePreloader();
        } else {
            window.addEventListener('load', hidePreloader, { once: true });
        }


    } catch (error) {
        console.error('Error loading theme scripts:', error);
        hidePreloader();
    }
});
</script>

<template>
    <div>
        <!-- Preloader Start -->
        <div v-show="preloaderVisible" class="preloader" style="z-index: 9999;">
            <div class="loading-container">
                <div class="loading"></div>
                <div id="loading-icon">
                    <img src="/storage/images/nbt/logo-transparent.png" alt="Loading">
                </div>
            </div>
        </div>
        <!-- Preloader End -->

        <!-- Main Content -->
        <div :style="{ visibility: preloaderVisible ? 'hidden' : 'visible' }">
            
            <!-- Header Start -->
            <header class="main-header py-2">
                <div class="header-sticky">
                    <nav class="navbar navbar-expand-lg">
                        <div class="container position-relative">
                            <!-- Logo Start -->
                            <Link :href="index()" class="navbar-brand">
                                <img src="/storage/images/nbt/logo_nbt_en.png" alt="Logo">
                            </Link>
                            <!-- Logo End -->

                            <!-- Main Menu Start -->
                            <div class="collapse navbar-collapse main-menu">
                                <div class="nav-menu-wrapper" @mouseleave="handleMouseLeave">
                                    <ul class="navbar-nav mr-auto" id="menu">
                                        <li v-for="(item, index) in menuItems" :key="index"
                                            class="nav-item submenu mega-menu"
                                            @mouseenter="handleMouseEnter(index)">
                                            
                                            <a class="nav-link" :href="item.link">{{ item.label }}</a>
                                            
                                            <!-- Hidden nested structure for SlickNav/SEO -->
                                            <div class="d-none">
                                                <ul>
                                                    <li v-for="sub in item.items" :key="sub.name">
                                                        <a href="#">{{ sub.name }}</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                
                                <!-- Header Btn Start -->
                                <div class="header-btn d-inline-flex align-items-center">
                                    <LanguageSwitcher />
                                    <Link :href="login()" class="btn-login">login</Link>
                                    <Link :href="register()" class="btn-default btn-signup">sign up</Link>
                                </div>
                                <!-- Header Btn End -->
                            </div>
                            <!-- Main Menu End -->
                            <div class="navbar-toggle"></div>
                        </div>
                    </nav>
                    <div class="responsive-menu"></div>
                    
                    <!-- Shared Desktop Mega Menu Content -->
                    <div class="mega-menu-shared-container mt-2" 
                         :class="{ 'active': isMenuOpen }"
                         @mouseenter="handleContentMouseEnter"
                         @mouseleave="handleMouseLeave">
                        <div class="container" v-if="displayIndex !== null">
                             <div class="mega-menu-grid">
                                <a :href="subItem.link" v-for="subItem in menuItems[displayIndex].items" 
                                   :key="subItem.name" 
                                   class="mega-menu-card">
                                    <div class="icon-box">
                                        <i :class="subItem.icon"></i>
                                    </div>
                                    <span class="item-name">{{ subItem.name }}</span>
                                </a>
                             </div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- Header End -->

            <!-- Page Content -->
            <slot />

            <!-- Security Footer -->
            <SecurityFooter />

             
        </div>
        <!-- Main Content End -->
    </div>
</template>


