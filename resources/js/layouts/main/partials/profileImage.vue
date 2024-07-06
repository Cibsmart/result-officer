<script lang="ts" setup>
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const pageProps = usePage();

const initials = computed(() => {
  return pageProps.props.auth.user.name
    .split(" ")
    .map((word) => word[0].toUpperCase())
    .join("");
});
</script>

<template>
  <span
    :class="['border-gray-200 text-gray-400 dark:border-gray-700 dark:bg-gray-800']"
    class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border bg-white text-[0.625rem] font-medium hover:border-indigo-600 hover:text-indigo-600 group-hover:border-indigo-600 group-hover:text-indigo-600 dark:bg-gray-800 dark:hover:border-white dark:hover:text-white dark:group-hover:border-gray-600 dark:group-hover:text-white">
    {{ initials }}
  </span>
</template>
