<script lang="ts" setup>
import Profile from "@/layouts/main/partials/profile.vue";
import { MagnifyingGlassIcon } from "@heroicons/vue/20/solid";
import { Bars3Icon, BellIcon } from "@heroicons/vue/24/outline";

const emit = defineEmits<(e: "openSidebar") => void>();

const openSidebar = () => {
  emit("openSidebar");
};
</script>

<template>
  <div
    class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8 dark:dark:bg-gray-900">
    <button
      class="-m-2.5 p-2.5 text-gray-700 lg:hidden dark:text-white"
      type="button"
      @click="openSidebar">
      <span class="sr-only">Open sidebar</span>

      <Bars3Icon
        aria-hidden="true"
        class="h-6 w-6" />
    </button>

    <!-- Separator -->
    <div
      aria-hidden="true"
      class="h-6 w-px bg-gray-200 lg:hidden dark:bg-gray-400" />

    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
      <form
        action="#"
        class="relative flex flex-1"
        method="GET">
        <label
          class="sr-only"
          for="search-field"
          >Search</label
        >

        <MagnifyingGlassIcon
          aria-hidden="true"
          class="pointer-events-none absolute inset-y-0 left-0 h-full w-5 text-gray-400" />

        <input
          id="search-field"
          class="block h-full w-full border-0 py-0 pl-8 pr-0 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm dark:bg-gray-900 dark:text-white"
          name="search"
          placeholder="Search..."
          type="search" />
      </form>

      <div class="flex items-center gap-x-4 lg:gap-x-6">
        <button
          class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300"
          type="button">
          <span class="sr-only">View notifications</span>

          <BellIcon
            aria-hidden="true"
            class="h-6 w-6" />
        </button>

        <!-- Separator -->
        <div
          aria-hidden="true"
          class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-400" />

        <Profile />
      </div>
    </div>
  </div>
</template>
