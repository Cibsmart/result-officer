<script lang="ts" setup>
import { ref } from "vue";
import Sidebar from "@/layouts/main/partials/sidebar.vue";
import Topbar from "@/layouts/main/partials/topbar.vue";
import SidebarMobile from "@/layouts/main/partials/sidebarMobile.vue";

const sidebarOpen = ref(false);
</script>

<template>
  <div>
    <SidebarMobile
      :sidebarOpen="sidebarOpen"
      @closeSidebar="sidebarOpen = false" />

    <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
      <Sidebar />
    </div>

    <div class="lg:pl-72">
      <Topbar @openSidebar="sidebarOpen = true" />

      <main class="pb-10">
        <header
          v-if="$slots.header"
          class="bg-white text-gray-800 shadow dark:bg-gray-800 dark:text-white">
          <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <slot name="header" />
          </div>
        </header>

        <div class="px-4 text-gray-800 sm:px-6 lg:px-8 dark:text-white">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>
