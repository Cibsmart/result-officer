<script lang="ts" setup>
import NavFooter from '@/components/NavFooter.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import AppLogo from './AppLogo.vue';
import { Navigation } from '@/components/sidebar';

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar
        collapsible="icon"
        variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton
                        as-child
                        size="lg">
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <Navigation />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />

            <NavUser />
        </SidebarFooter>
    </Sidebar>

    <slot />
</template>
