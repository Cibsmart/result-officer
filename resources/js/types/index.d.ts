import type { FunctionalComponent, HTMLAttributes, VNodeProps } from "vue";

type IconType = FunctionalComponent<HTMLAttributes, VNodeProps>;

export interface NavigationItem {
  name: string;
  href: string;
  icon?: IconType;
  current: boolean;
  children?: NavigationItem[];
}

export interface UserNavigationItem {
  name: string;
  href: string;
  method: "get" | "post" | "put" | "delete";
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
  auth: {
    user: App.Data.Shared.UserData;
  };
};
