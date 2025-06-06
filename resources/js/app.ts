import '../css/app.css';
// import "./bootstrap";
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, DefineComponent, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';
import { notifications } from '@/plugins/notifications';
import { initializeTheme } from '@/composables/useAppearance';

// import Echo from "laravel-echo";
//
// import Pusher from "pusher-js";
//
// window.Pusher = Pusher;
//
// window.Echo = new Echo({
//   broadcaster: "reverb",
//   key: import.meta.env.VITE_REVERB_APP_KEY,
//   wsHost: import.meta.env.VITE_REVERB_HOST,
//   wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
//   wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
//   forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? "http") === "https",
//   enabledTransports: ["ws", "wss"],
// });

const appName = import.meta.env.VITE_APP_NAME || 'ResultPro';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: async (name: string) =>
        resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(Toast)
            .use(notifications)
            .mount(el);
    },
    progress: {
        color: '#6366f1',
        includeCSS: true,
        showSpinner: true,
    },
});

// This will set light / dark mode on page load...
initializeTheme();
