<script lang="ts" setup>
import { SidebarGroup, SidebarGroupLabel, SidebarMenu } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { MenuItemCollapsible, MenuItemSingle } from '@/components/sidebar';

defineProps<{
    items: NavItem[];
    label: string;
}>();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>{{ label }}</SidebarGroupLabel>

        <SidebarMenu>
            <template
                v-for="item in items"
                :key="item.title">
                <MenuItemCollapsible
                    v-if="Object.hasOwn(item, 'items')"
                    :item="item" />

                <MenuItemSingle
                    v-else
                    :item="item" />
            </template>
        </SidebarMenu>
    </SidebarGroup>
</template>
