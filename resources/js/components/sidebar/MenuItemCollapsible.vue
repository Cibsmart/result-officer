<script lang="ts" setup>
import { Link } from '@inertiajs/vue3';
import page from '@/pages/vetting/show/page.vue';
import {
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { NavItem } from '@/types';
import { ChevronRight } from 'lucide-vue-next';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';

defineProps<{ item: NavItem }>();
</script>

<template>
    <Collapsible
        :defaultOpen="item.isActive"
        asChild>
        <SidebarMenuItem>
            <CollapsibleTrigger asChild>
                <SidebarMenuButton :tooltip="item.title">
                    <component :is="item.icon" />

                    <span>{{ item.title }}</span>

                    <ChevronRight
                        className="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                </SidebarMenuButton>
            </CollapsibleTrigger>

            <CollapsibleContent>
                <SidebarMenuSub>
                    <SidebarMenuSubItem
                        v-for="subItem in item?.items"
                        :key="subItem.title">
                        <SidebarMenuSubButton
                            :is-active="subItem.href === page.url"
                            :tooltip="subItem.title"
                            asChild>
                            <Link :href="subItem.href">
                                <span>{{ subItem.title }}</span>
                            </Link>
                        </SidebarMenuSubButton>
                    </SidebarMenuSubItem>
                </SidebarMenuSub>
            </CollapsibleContent>
        </SidebarMenuItem>
    </Collapsible>
</template>
