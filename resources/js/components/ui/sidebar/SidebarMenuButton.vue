<script lang="ts" setup>
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import { type Component, computed } from 'vue';
import SidebarMenuButtonChild, { type SidebarMenuButtonProps } from './SidebarMenuButtonChild.vue';
import { useSidebar } from './utils';

const props = withDefaults(
    defineProps<
        SidebarMenuButtonProps & {
            tooltip?: string | Component;
        }
    >(),
    {
        as: 'button',
        variant: 'default',
        size: 'default',
    },
);

defineOptions({
    inheritAttrs: false,
});

const { isMobile, state } = useSidebar();

const delegatedProps = computed(() => {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const { tooltip, ...delegated } = props;
    return delegated;
});
</script>

<template>
    <SidebarMenuButtonChild
        v-if="!tooltip"
        v-bind="{ ...delegatedProps, ...$attrs }">
        <slot />
    </SidebarMenuButtonChild>

    <Tooltip v-else>
        <TooltipTrigger as-child>
            <SidebarMenuButtonChild v-bind="{ ...delegatedProps, ...$attrs }">
                <slot />
            </SidebarMenuButtonChild>
        </TooltipTrigger>

        <TooltipContent
            :hidden="state !== 'collapsed' || isMobile"
            align="center"
            side="right">
            <template v-if="typeof tooltip === 'string'">
                {{ tooltip }}
            </template>

            <component
                :is="tooltip"
                v-else />
        </TooltipContent>
    </Tooltip>
</template>
