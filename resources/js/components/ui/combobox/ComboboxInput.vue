<script lang="ts" setup>
import { cn } from '@/lib/utils';
import { SearchIcon } from 'lucide-vue-next';
import { ComboboxInput, type ComboboxInputEmits, type ComboboxInputProps, useForwardPropsEmits } from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';

const props = defineProps<
    ComboboxInputProps & {
        class?: HTMLAttributes['class'];
    }
>();

const emits = defineEmits<ComboboxInputEmits>();

defineOptions({
    inheritAttrs: false,
});

const delegatedProps = computed(() => {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const { class: _, ...delegated } = props;

    return delegated;
});

const forwarded = useForwardPropsEmits(delegatedProps, emits);
</script>

<template>
    <div
        class="flex h-9 items-center gap-2 border-b px-3"
        data-slot="command-input-wrapper">
        <SearchIcon class="size-4 shrink-0 opacity-50" />

        <ComboboxInput
            :class="
                cn(
                    'placeholder:text-muted-foreground flex h-10 w-full rounded-md bg-transparent py-3 text-sm outline-hidden disabled:cursor-not-allowed disabled:opacity-50',
                    props.class,
                )
            "
            data-slot="command-input"
            v-bind="{ ...forwarded, ...$attrs }">
            <slot />
        </ComboboxInput>
    </div>
</template>
