<script lang="ts" setup>
import type { ComboboxEmptyProps } from 'reka-ui';
import { cn } from '@/lib/utils';
import { ComboboxEmpty } from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';

const props = defineProps<ComboboxEmptyProps & { class?: HTMLAttributes['class'] }>();

const delegatedProps = computed(() => {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const { class: _, ...delegated } = props;

    return delegated;
});
</script>

<template>
    <ComboboxEmpty
        :class="cn('py-6 text-center text-sm', props.class)"
        data-slot="combobox-empty"
        v-bind="delegatedProps">
        <slot />
    </ComboboxEmpty>
</template>
