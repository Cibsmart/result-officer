<script lang="ts" setup>
import { computed } from "vue";

const props = defineProps<{
  name: string;
}>();

const initials = computed(() =>
  props.name
    .split(" ")
    .map((word) => word[0])
    .join("")
    .slice(0, 2),
);
</script>

<template>
  <span
    class="flex size-16 shrink-0 items-center justify-center rounded-full border border-indigo-100 bg-indigo-200/20 text-3xl font-bold text-indigo-600/60 dark:border-indigo-200 dark:bg-indigo-200">
    {{ initials }}
  </span>
</template>
