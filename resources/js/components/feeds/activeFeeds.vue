<script lang="ts" setup>
import { computed } from "vue";

const props = defineProps<{
  data: App.Data.Import.PendingImportEventData;
}>();

const progressWidth = computed(() => `width: ${props.data.width}%`);
</script>

<template>
  <div>
    <h4 class="sr-only">Status</h4>

    <div
      aria-hidden="true"
      class="mt-6">
      <div class="animate-pulse overflow-hidden rounded-full bg-gray-200 dark:bg-gray-600">
        <div
          :style="progressWidth"
          class="transition-2s h-2 rounded-full bg-indigo-600 transition-all ease-in-out dark:bg-indigo-300" />
      </div>

      <div class="mt-2 hidden justify-between text-sm font-medium text-gray-600 sm:flex dark:text-gray-400">
        <template
          v-for="(element, index) in data.elements"
          :key="index">
          <div class="text-center first:text-start last:text-end">{{ element }}</div>
        </template>
      </div>
    </div>
  </div>
</template>
