<script lang="ts" setup>
import { Table, TableCaption } from '@/components/ui/table';

defineProps<{
    title?: string;
}>();
</script>

<template>
    <Table>
        <TableCaption v-if="title">{{ title }}</TableCaption>

        <slot />
    </Table>
</template>
