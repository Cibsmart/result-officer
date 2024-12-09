<script lang="ts" setup>
import Badge from "@/components/badge.vue";
import SectionHeader from "@/components/sectionHeader.vue";
import HamburgerMenu from "@/components/hamburgerMenu.vue";
import { NavigationItem } from "@/types";

const menus: NavigationItem[] = [
  { name: "Edit", href: "#" },
  { name: "Delete", href: "#" },
];
</script>

<template>
  <div class="flex items-start justify-between">
    <div class="sm:w-0 sm:flex-1">
      <div class="flex items-start space-x-5">
        <div class="shrink-0">
          <div class="relative">
            <img
              alt=""
              class="size-16 rounded-full"
              src="https://images.unsplash.com/photo-1463453091185-61582044d556?ixlib=rb-=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=8&w=1024&h=1024&q=80" />

            <span
              aria-hidden="true"
              class="absolute inset-0 rounded-full shadow-inner" />
          </div>
        </div>

        <div class="pt-1">
          <SectionHeader description="EBSU/2009/51486"> Ifebude Barnabas</SectionHeader>
        </div>
      </div>
    </div>

    <div class="mt-2 flex items-center justify-between sm:ml-6 sm:shrink-0 sm:justify-start">
      <Badge color="green">Active</Badge>

      <HamburgerMenu :menus="menus" />
    </div>
  </div>
</template>
