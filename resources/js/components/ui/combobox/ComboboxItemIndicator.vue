<script lang="ts" setup>
import type { ComboboxItemIndicatorProps } from 'reka-ui';
import { cn } from '@/lib/utils';
import { ComboboxItemIndicator, useForwardProps } from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';

const props = defineProps<ComboboxItemIndicatorProps & { class?: HTMLAttributes['class'] }>();

const delegatedProps = computed(() => {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const { class: _, ...delegated } = props;

    return delegated;
});

const forwarded = useForwardProps(delegatedProps);
</script>

<template>
    <ComboboxItemIndicator
        :class="cn('ml-auto', props.class)"
        data-slot="combobox-item-indicator"
        v-bind="forwarded">
        <slot />
    </ComboboxItemIndicator>
</template>
