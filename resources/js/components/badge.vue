<script lang="ts" setup>
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        color: App.Enums.StatusColor;
    }>(),
    {
        color: 'gray',
    },
);

const mainClasses = computed(() => {
    return {
        gray: 'bg-gray-100 text-gray-600 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20',
        blue: 'bg-blue-100 text-blue-700 dark:bg-blue-400/10 dark:text-blue-400 dark:ring-blue-400/30',
        green: 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20',
        yellow: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-400/10 dark:text-yellow-500 dark:ring-yellow-400/20',
        pink: 'bg-pink-100 text-pink-700 dark:bg-pink-400/10 dark:text-pink-400 dark:ring-pink-400/20',
        indigo: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-400/10 dark:text-indigo-400 dark:ring-indigo-400/30',
        red: 'bg-red-100 text-red-700 dark:bg-red-400/10 dark:text-red-400 dark:ring-red-400/20',
        purple: 'bg-purple-100 text-purple-700 dark:bg-purple-400/10 dark:text-purple-400 dark:ring-purple-400/30',
    }[props.color];
});

const svgClasses = computed(() => {
    return {
        gray: 'fill-gray-400',
        blue: 'fill-blue-500',
        green: 'fill-green-500',
        yellow: 'fill-yellow-500',
        pink: 'fill-pink-500',
        indigo: 'fill-indigo-500',
        red: 'fill-red-500',
        purple: 'fill-purple-500',
    }[props.color];
});
</script>

<template>
    <span
        :class="mainClasses"
        class="inline-flex items-center gap-x-1.5 rounded-full px-1.5 py-0.5 text-xs font-medium dark:ring-1 dark:ring-inset">
        <svg
            :class="svgClasses"
            aria-hidden="true"
            class="size-1.5"
            viewBox="0 0 6 6">
            <circle
                cx="3"
                cy="3"
                r="3" />
        </svg>

        <slot />
    </span>
</template>
