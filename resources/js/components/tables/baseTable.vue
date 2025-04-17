<script lang="ts" setup>
defineProps<{
    title?: string;
}>();
</script>

<template>
    <div class="-mx-4 overflow-auto ring-1 ring-gray-300 sm:mx-0 sm:rounded-lg dark:ring-gray-600">
        <div
            v-if="title"
            class="min-w-full pt-2 text-center text-sm font-extrabold">
            {{ title }}
        </div>

        <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
            <slot />
        </table>
    </div>
</template>
