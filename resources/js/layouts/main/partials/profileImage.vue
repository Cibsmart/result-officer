<script lang="ts" setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";

const initials = computed(() => {
  return usePage()
    .props.auth.user.name.split(" ")
    .map((word) => word[0].toUpperCase())
    .join("");
});
</script>

<template>
  <span
    class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white text-[0.625rem] font-medium text-gray-400 hover:border-indigo-600 hover:text-indigo-600 group-hover:border-indigo-600 group-hover:text-indigo-600 dark:border-gray-700 dark:bg-gray-800 dark:hover:border-white dark:hover:text-white dark:group-hover:border-gray-600 dark:group-hover:text-white">
    {{ initials }}
  </span>
</template>
