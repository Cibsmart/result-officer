<script lang="ts" setup>
import { Link } from '@inertiajs/vue3';

interface Props {
    href: string;
    tabindex?: number;
    as?: string;
}

defineProps<Props>();
</script>

<template>
    <Link
        :as="as"
        :href="href"
        :tabindex="tabindex"
        class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500">
        <slot />
    </Link>
</template>
