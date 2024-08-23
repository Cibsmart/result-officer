<script lang="ts" setup>
defineProps<{
  header: string;
  description: string;
}>();
</script>

<template>
  <section class="max-w-xl">
    <header>
      <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ header }}</h2>

      <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
        {{ description }}
      </p>
    </header>

    <slot />
  </section>
</template>
