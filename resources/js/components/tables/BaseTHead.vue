<script lang="ts" setup>
import { TableHeader, TableRow } from '@/components/ui/table';
</script>

<template>
    <TableHeader>
        <TableRow>
            <slot />
        </TableRow>
    </TableHeader>
</template>
