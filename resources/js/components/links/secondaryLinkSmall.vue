<script lang="ts" setup>
import { Link } from "@inertiajs/vue3";

const props = defineProps<{
  href: string;
}>();
</script>

<template>
  <Link
    :href="props.href"
    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-2 py-1 text-xs font-semibold tracking-widest text-gray-700 uppercase shadow-xs transition duration-150 ease-in-out hover:bg-gray-50 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden disabled:opacity-25 dark:border-gray-500 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-800">
    <slot />
  </Link>
</template>
