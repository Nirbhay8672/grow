
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import InertiaLoader from './components/InertiaLoader.vue';
import { router } from '@inertiajs/vue3';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => {
        const safeName = name || 'Dashboard';
        return resolvePageComponent(
            `./pages/${safeName}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        );
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.use(plugin).mount(el);
        
        // Mount loader to body as a separate app instance
        const loaderEl = document.createElement('div');
        loaderEl.id = 'inertia-loader';
        document.body.appendChild(loaderEl);
        createApp(InertiaLoader).mount('#inertia-loader');
        
        // Make Inertia router available globally for Blade template links
        (window as any).Inertia = router;
    },
    progress: {
        color: '#4B5563',
        showSpinner: true,
    },
});

// Default Laravel kit theme initialization removed
