<script lang="ts" setup>
import type { ComboboxGroupProps } from 'reka-ui';
import { cn } from '@/lib/utils';
import { ComboboxGroup, ComboboxLabel } from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';

const props = defineProps<
    ComboboxGroupProps & {
        class?: HTMLAttributes['class'];
        heading?: string;
    }
>();

const delegatedProps = computed(() => {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const { class: _, ...delegated } = props;

    return delegated;
});
</script>

<template>
    <ComboboxGroup
        :class="cn('text-foreground overflow-hidden p-1', props.class)"
        data-slot="combobox-group"
        v-bind="delegatedProps">
        <ComboboxLabel
            v-if="heading"
            class="text-muted-foreground px-2 py-1.5 text-xs font-medium">
            {{ heading }}
        </ComboboxLabel>

        <slot />
    </ComboboxGroup>
</template>
