<script lang="ts" setup>
import { Bars3Icon } from "@heroicons/vue/24/outline";
import ProfileImage from "@/layouts/main/partials/profileImage.vue";

const emit = defineEmits<(e: "openSidebar") => void>();

const openSidebar = () => {
  emit("openSidebar");
};
</script>

<template>
  <div
    class="sticky top-0 z-40 flex items-center gap-x-6 bg-white px-4 py-4 shadow-sm sm:px-6 lg:hidden dark:dark:bg-gray-900">
    <button
      class="-m-2.5 p-2.5 text-gray-700 lg:hidden dark:text-white"
      type="button"
      @click="openSidebar">
      <span class="sr-only">Open sidebar</span>

      <Bars3Icon
        aria-hidden="true"
        class="h-6 w-6" />
    </button>

    <div class="flex-1 text-sm font-semibold leading-6 text-gray-900 dark:text-white">ResultPro</div>

    <a href="#">
      <span class="sr-only">Your profile</span>

      <ProfileImage />
    </a>
  </div>
</template>
