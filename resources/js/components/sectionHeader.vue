<script lang="ts" setup>
defineProps<{
  description?: string;
}>();
</script>

<template>
  <header>
    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
      <slot />
    </h2>

    <template v-if="description">
      <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
        {{ description }}
      </p>
    </template>
  </header>
</template>
