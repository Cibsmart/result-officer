<script lang="ts" setup>
import { cn } from '@/lib/utils';
import { DialogTitle, type DialogTitleProps } from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';

const props = defineProps<DialogTitleProps & { class?: HTMLAttributes['class'] }>();

const delegatedProps = computed(() => {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const { class: _, ...delegated } = props;

    return delegated;
});
</script>

<template>
    <DialogTitle
        :class="cn('text-foreground font-semibold', props.class)"
        data-slot="sheet-title"
        v-bind="delegatedProps">
        <slot />
    </DialogTitle>
</template>
