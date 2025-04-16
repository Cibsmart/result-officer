import type { FunctionalComponent, HTMLAttributes, VNodeProps } from 'vue';
import type { LucideIcon } from 'lucide-vue-next';
import SharedData = App.Data.Shared.SharedData;

type IconType = FunctionalComponent<HTMLAttributes, VNodeProps>;

export interface NavigationItem {
    name: string;
    href: string;
    icon?: IconType;
    current?: boolean;
    children?: NavigationItem[];
}

export interface UserNavigationItem {
    name: string;
    href: string;
    method: 'get' | 'post' | 'put' | 'delete';
}

export interface BreadcrumbsItem {
    name: string;
    href: string;
    current: boolean;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface TabItem {
    name: string;
}

export interface SelectItem {
    id: number;
    name: string;
}

export interface EnumSelectItem {
    id: string;
    name: string;
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & SharedData;

export type BreadcrumbItemType = BreadcrumbItem;
