<script lang="ts" setup>
const props = defineProps<{
  href: string;
}>();
</script>

<template>
  <a
    :href="props.href"
    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase shadow-xs transition duration-150 ease-in-out hover:bg-gray-50 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden disabled:opacity-25 dark:border-gray-500 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-800"
    target="_blank">
    <slot />
  </a>
</template>
