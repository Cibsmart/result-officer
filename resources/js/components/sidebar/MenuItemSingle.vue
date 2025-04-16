<script lang="ts" setup>
import { Link } from '@inertiajs/vue3';
import { SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { NavItem } from '@/types';

defineProps<{ item: NavItem }>();
</script>

<template>
    <SidebarMenuItem>
        <SidebarMenuButton
            :is-active="item.isActive"
            :tooltip="item.title"
            as-child>
            <Link :href="item.href">
                <component :is="item.icon" />

                <span>{{ item.title }}</span>
            </Link>
        </SidebarMenuButton>
    </SidebarMenuItem>
</template>
