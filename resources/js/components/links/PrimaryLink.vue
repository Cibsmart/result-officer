<script lang="ts" setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    href: string;
}>();
</script>

<template>
    <Link
        :href="props.href"
        class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white dark:focus:bg-white dark:focus:ring-offset-gray-800">
        <slot />
    </Link>
</template>
