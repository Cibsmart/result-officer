<script lang="ts" setup>
import { cn } from '@/lib/utils';
import { DropdownMenuSeparator, type DropdownMenuSeparatorProps } from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';

const props = defineProps<
    DropdownMenuSeparatorProps & {
        class?: HTMLAttributes['class'];
    }
>();

const delegatedProps = computed(() => {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const { class: _, ...delegated } = props;

    return delegated;
});
</script>

<template>
    <DropdownMenuSeparator
        :class="cn('bg-border -mx-1 my-1 h-px', props.class)"
        data-slot="dropdown-menu-separator"
        v-bind="delegatedProps" />
</template>
