<script lang="ts" setup>
import { ref } from 'vue';
import Sidebar from '@/layouts/main/partials/sidebar.vue';
import Topbar from '@/layouts/main/partials/topbar.vue';
import SidebarMobile from '@/layouts/main/partials/sidebarMobile.vue';

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
                <slot />
            </main>
        </div>
    </div>
</template>
