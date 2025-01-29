<script lang="ts" setup>
import { Menu, MenuButton, MenuItems } from "@headlessui/vue";
import { EllipsisVerticalIcon, EllipsisHorizontalIcon } from "@heroicons/vue/20/solid";

withDefaults(
  defineProps<{
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
        class="-my-2 flex items-center rounded-full bg-white p-1.5 text-gray-400 hover:text-gray-600 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden dark:bg-gray-800 dark:text-gray-200 dark:hover:text-gray-50">
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
        class="absolute right-0 z-10 mt-2 w-40 origin-top-right rounded-md bg-white ring-1 shadow-lg ring-black/5 focus:outline-hidden dark:bg-gray-900 dark:ring-white/20">
        <div class="py-1">
          <slot />
        </div>
      </MenuItems>
    </transition>
  </Menu>
</template>
