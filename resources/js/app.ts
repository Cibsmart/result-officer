import "../css/app.css";

import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, DefineComponent, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import LayoutMain from "@/layouts/main/layoutMain.vue";
import LayoutGuest from "@/layouts/guest/layoutGuest.vue";

const appName = import.meta.env.VITE_APP_NAME || "ResultPro";

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: async (name: string) => {
    const page = await resolvePageComponent(
      `./pages/${name}.vue`,
      import.meta.glob<DefineComponent>("./pages/**/*.vue"),
    );

    const layout = name.startsWith("auth/") ? LayoutGuest : LayoutMain;

    page.default.layout = page.default.layout || layout;

    return page;
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
      .mount(el);
  },
  progress: {
    color: "#4B5563",
  },
});
