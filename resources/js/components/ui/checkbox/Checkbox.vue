<script lang="ts" setup>
import type { CheckboxRootEmits, CheckboxRootProps } from 'reka-ui';
import { cn } from '@/lib/utils';
import { Check } from 'lucide-vue-next';
import { CheckboxIndicator, CheckboxRoot, useForwardPropsEmits } from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';

const props = defineProps<CheckboxRootProps & { class?: HTMLAttributes['class'] }>();
const emits = defineEmits<CheckboxRootEmits>();

const delegatedProps = computed(() => {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const { class: _, ...delegated } = props;

    return delegated;
});

const forwarded = useForwardPropsEmits(delegatedProps, emits);
</script>

<template>
    <CheckboxRoot
        :class="
            cn(
                'peer border-input data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground data-[state=checked]:border-primary focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive size-4 shrink-0 rounded-[4px] border shadow-xs transition-shadow outline-none focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50',
                props.class,
            )
        "
        data-slot="checkbox"
        v-bind="forwarded">
        <CheckboxIndicator
            class="flex items-center justify-center text-current transition-none"
            data-slot="checkbox-indicator">
            <slot>
                <Check class="size-3.5" />
            </slot>
        </CheckboxIndicator>
    </CheckboxRoot>
</template>
