<script lang="ts" setup>
defineProps<{ value?: any }>();

const checked = defineModel<any[] | boolean>("modelValue");
</script>

<template>
  <input
    v-model="checked"
    :value="value"
    class="rounded-sm border-gray-300 text-indigo-600 shadow-xs focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
    type="checkbox" />
</template>
