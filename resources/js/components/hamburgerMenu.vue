<script lang="ts" setup>
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { EllipsisVerticalIcon, EllipsisHorizontalIcon } from "@heroicons/vue/20/solid";
import { NavigationItem } from "@/types";
import { Link } from "@inertiajs/vue3";

withDefaults(
  defineProps<{
    menus: NavigationItem[];
    orientation?: "vertical" | "horizontal";
  }>(),
  {
    orientation: "vertical",
  },
);
</script>

<template>
  <Menu
    as="div"
    class="relative ml-3 inline-block text-left">
    <div>
      <MenuButton
        class="-my-2 flex items-center rounded-full bg-white p-2 text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-200 dark:hover:text-gray-50">
        <span class="sr-only">Open options</span>

        <EllipsisHorizontalIcon
          v-if="orientation === 'horizontal'"
          aria-hidden="true"
          class="size-5" />

        <EllipsisVerticalIcon
          v-else
          aria-hidden="true"
          class="size-5" />
      </MenuButton>
    </div>

    <transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95">
      <MenuItems
        class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none dark:bg-gray-900 dark:ring-white/20">
        <div class="py-1">
          <template
            v-for="(menu, index) in menus"
            :key="index">
            <MenuItem v-slot="{ active }">
              <Link
                :class="[
                  active
                    ? 'bg-gray-100 text-gray-900 outline-none dark:bg-gray-800 dark:text-white'
                    : 'text-gray-700 dark:text-gray-200',
                ]"
                :href="menu.href"
                class="flex justify-between px-4 py-2 text-sm">
                <span>{{ menu.name }}</span>
              </Link>
            </MenuItem>
          </template>
        </div>
      </MenuItems>
    </transition>
  </Menu>
</template>
