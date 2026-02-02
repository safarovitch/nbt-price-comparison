import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { setUrlDefaults } from '@/wayfinder';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { initializeTheme } from './composables/useAppearance';

// jQuery for theme compatibility
import jQuery from 'jquery';

// Bootstrap JavaScript
import * as bootstrap from 'bootstrap';

// Make jQuery and Bootstrap available globally for theme scripts
declare global {
    interface Window {
        $: typeof jQuery;
        jQuery: typeof jQuery;
        bootstrap: typeof bootstrap;
    }
}

window.$ = window.jQuery = jQuery;
window.bootstrap = bootstrap;

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const locale = props.initialPage.props.locale as string;
        setUrlDefaults({ locale });

        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
