<script lang="ts" setup>
import { MenuItem } from "@headlessui/vue";
import { Link } from "@inertiajs/vue3";

defineProps<{ href: string }>();
</script>

<template>
  <MenuItem v-slot="{ active }">
    <Link
      :class="[
        active
          ? 'bg-gray-100 text-gray-900 outline-hidden dark:bg-gray-800 dark:text-white'
          : 'text-gray-700 dark:text-gray-200',
      ]"
      :href="href"
      class="flex justify-between px-4 py-2 text-sm">
      <span>
        <slot />
      </span>
    </Link>
  </MenuItem>
</template>

<style scoped></style>
