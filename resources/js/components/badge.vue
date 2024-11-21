<script lang="ts" setup>
withDefaults(
  defineProps<{
    color: App.Enums.StatusColor;
  }>(),
  {
    color: "gray",
  },
);
</script>

<template>
  <span
    v-if="color === 'gray'"
    class="inline-flex items-center gap-x-1.5 rounded-full bg-gray-100 px-1.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-1 dark:ring-inset dark:ring-gray-400/20">
    <svg
      aria-hidden="true"
      class="size-1.5 fill-gray-400"
      viewBox="0 0 6 6">
      <circle
        cx="3"
        cy="3"
        r="3" />
    </svg>

    <slot />
  </span>

  <span
    v-if="color === 'red'"
    class="inline-flex items-center gap-x-1.5 rounded-full bg-red-100 px-1.5 py-0.5 text-xs font-medium text-red-700 dark:bg-red-400/10 dark:text-red-400 dark:ring-1 dark:ring-inset dark:ring-red-400/20">
    <svg
      aria-hidden="true"
      class="size-1.5 fill-red-500"
      viewBox="0 0 6 6">
      <circle
        cx="3"
        cy="3"
        r="3" />
    </svg>

    <slot />
  </span>

  <span
    v-if="color === 'yellow'"
    class="inline-flex items-center gap-x-1.5 rounded-full bg-yellow-100 px-1.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-400/10 dark:text-yellow-500 dark:ring-1 dark:ring-inset dark:ring-yellow-400/20">
    <svg
      aria-hidden="true"
      class="size-1.5 fill-yellow-500"
      viewBox="0 0 6 6">
      <circle
        cx="3"
        cy="3"
        r="3" />
    </svg>

    <slot />
  </span>

  <span
    v-if="color === 'green'"
    class="inline-flex items-center gap-x-1.5 rounded-full bg-green-100 px-1.5 py-0.5 text-xs font-medium text-green-700 dark:bg-green-500/10 dark:text-green-400 dark:ring-1 dark:ring-inset dark:ring-green-500/20">
    <svg
      aria-hidden="true"
      class="size-1.5 fill-green-500"
      viewBox="0 0 6 6">
      <circle
        cx="3"
        cy="3"
        r="3" />
    </svg>

    <slot />
  </span>

  <span
    v-if="color === 'blue'"
    class="inline-flex items-center gap-x-1.5 rounded-full bg-blue-100 px-1.5 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-400/10 dark:text-blue-400 dark:ring-1 dark:ring-inset dark:ring-blue-400/30">
    <svg
      aria-hidden="true"
      class="size-1.5 fill-blue-500"
      viewBox="0 0 6 6">
      <circle
        cx="3"
        cy="3"
        r="3" />
    </svg>

    <slot />
  </span>

  <span
    v-if="color === 'indigo'"
    class="inline-flex items-center gap-x-1.5 rounded-full bg-indigo-100 px-1.5 py-0.5 text-xs font-medium text-indigo-700 dark:bg-indigo-400/10 dark:text-indigo-400 dark:ring-1 dark:ring-inset dark:ring-indigo-400/30">
    <svg
      aria-hidden="true"
      class="size-1.5 fill-indigo-500"
      viewBox="0 0 6 6">
      <circle
        cx="3"
        cy="3"
        r="3" />
    </svg>

    <slot />
  </span>

  <span
    v-if="color === 'purple'"
    class="inline-flex items-center gap-x-1.5 rounded-full bg-purple-100 px-1.5 py-0.5 text-xs font-medium text-purple-700 dark:bg-purple-400/10 dark:text-purple-400 dark:ring-1 dark:ring-inset dark:ring-purple-400/30">
    <svg
      aria-hidden="true"
      class="size-1.5 fill-purple-500"
      viewBox="0 0 6 6">
      <circle
        cx="3"
        cy="3"
        r="3" />
    </svg>

    <slot />
  </span>

  <span
    v-if="color === 'pink'"
    class="inline-flex items-center gap-x-1.5 rounded-full bg-pink-100 px-1.5 py-0.5 text-xs font-medium text-pink-700 dark:bg-pink-400/10 dark:text-pink-400 dark:ring-1 dark:ring-inset dark:ring-pink-400/20">
    <svg
      aria-hidden="true"
      class="size-1.5 fill-pink-500"
      viewBox="0 0 6 6">
      <circle
        cx="3"
        cy="3"
        r="3" />
    </svg>

    <slot />
  </span>
</template>
