<script lang="ts" setup>
import BaseTH from '@/components/tables/BaseTH.vue';
import { Link } from '@inertiajs/vue3';
import { ArrowDown, ArrowUp, ChevronsUpDown } from 'lucide-vue-next';

defineProps<{
    href: string;
    active: boolean;
    direction: App.Enums.SortDirection;
}>();
</script>

<template>
    <BaseTH>
        <Link
            :href="href"
            class="group inline-flex items-center gap-1 hover:text-gray-900 dark:hover:text-white"
            preserve-scroll>
            <slot />

            <ArrowUp
                v-if="active && direction === 'asc'"
                aria-hidden="true"
                class="size-3.5" />

            <ArrowDown
                v-else-if="active"
                aria-hidden="true"
                class="size-3.5" />

            <ChevronsUpDown
                v-else
                aria-hidden="true"
                class="size-3.5 opacity-0 group-hover:opacity-50" />
        </Link>
    </BaseTH>
</template>
