<script lang="ts" setup>
defineProps<{
  title: string;
}>();
</script>

<template>
  <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
    <dt class="text-sm/6 font-medium text-gray-900 dark:text-white">{{ title }}</dt>

    <dd class="mt-1 text-sm/6 text-gray-800 sm:col-span-2 sm:mt-0 dark:text-gray-200">
      <slot />
    </dd>
  </div>
</template>
