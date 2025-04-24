<script lang="ts" setup>
import type { ComboboxSeparatorProps } from 'reka-ui';
import { cn } from '@/lib/utils';
import { ComboboxSeparator } from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';

const props = defineProps<ComboboxSeparatorProps & { class?: HTMLAttributes['class'] }>();

const delegatedProps = computed(() => {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const { class: _, ...delegated } = props;

    return delegated;
});
</script>

<template>
    <ComboboxSeparator
        :class="cn('bg-border -mx-1 h-px', props.class)"
        data-slot="combobox-separator"
        v-bind="delegatedProps">
        <slot />
    </ComboboxSeparator>
</template>
