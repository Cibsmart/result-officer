<script lang="ts" setup>
import { cn } from '@/lib/utils';
import { computed, type HTMLAttributes } from 'vue';
import TableCell from './TableCell.vue';
import TableRow from './TableRow.vue';

const props = withDefaults(
    defineProps<{
        class?: HTMLAttributes['class'];
        colspan?: number;
    }>(),
    {
        colspan: 1,
    },
);

const delegatedProps = computed(() => {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const { class: _, ...delegated } = props;

    return delegated;
});
</script>

<template>
    <TableRow>
        <TableCell
            :class="cn('text-foreground p-4 align-middle text-sm whitespace-nowrap', props.class)"
            v-bind="delegatedProps">
            <div class="flex items-center justify-center py-10">
                <slot />
            </div>
        </TableCell>
    </TableRow>
</template>
