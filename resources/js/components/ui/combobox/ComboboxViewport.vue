<script lang="ts" setup>
import type { ComboboxViewportProps } from 'reka-ui';
import { cn } from '@/lib/utils';
import { ComboboxViewport, useForwardProps } from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';

const props = defineProps<ComboboxViewportProps & { class?: HTMLAttributes['class'] }>();

const delegatedProps = computed(() => {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const { class: _, ...delegated } = props;

    return delegated;
});

const forwarded = useForwardProps(delegatedProps);
</script>

<template>
    <ComboboxViewport
        :class="cn('max-h-[300px] scroll-py-1 overflow-x-hidden overflow-y-auto', props.class)"
        data-slot="combobox-viewport"
        v-bind="forwarded">
        <slot />
    </ComboboxViewport>
</template>
